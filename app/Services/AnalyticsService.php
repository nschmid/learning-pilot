<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Enums\UserRole;
use App\Models\Enrollment;
use App\Models\LearningPath;
use App\Models\TaskSubmission;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Get platform-wide statistics.
     */
    public function getPlatformStats(): array
    {
        return [
            'users' => [
                'total' => User::count(),
                'active' => User::where('is_active', true)->count(),
                'admins' => User::where('role', UserRole::Admin)->count(),
                'instructors' => User::where('role', UserRole::Instructor)->count(),
                'learners' => User::where('role', UserRole::Learner)->count(),
                'new_this_month' => User::where('created_at', '>=', now()->startOfMonth())->count(),
                'new_this_week' => User::where('created_at', '>=', now()->startOfWeek())->count(),
            ],
            'teams' => [
                'total' => Team::count(),
                'new_this_month' => Team::where('created_at', '>=', now()->startOfMonth())->count(),
            ],
            'paths' => [
                'total' => LearningPath::count(),
                'published' => LearningPath::where('is_published', true)->count(),
                'draft' => LearningPath::where('is_published', false)->count(),
                'new_this_month' => LearningPath::where('created_at', '>=', now()->startOfMonth())->count(),
            ],
            'enrollments' => [
                'total' => Enrollment::count(),
                'active' => Enrollment::where('status', EnrollmentStatus::Active)->count(),
                'completed' => Enrollment::where('status', EnrollmentStatus::Completed)->count(),
                'paused' => Enrollment::where('status', EnrollmentStatus::Paused)->count(),
                'new_this_month' => Enrollment::where('created_at', '>=', now()->startOfMonth())->count(),
            ],
            'submissions' => [
                'total' => TaskSubmission::count(),
                'pending' => TaskSubmission::where('status', 'pending')->count(),
                'reviewed_this_week' => TaskSubmission::where('reviewed_at', '>=', now()->startOfWeek())->count(),
            ],
        ];
    }

    /**
     * Get enrollment trends over time.
     */
    public function getEnrollmentTrends(int $months = 6): Collection
    {
        return Enrollment::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('COUNT(*) as total'),
            DB::raw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed")
        )
            ->where('created_at', '>=', now()->subMonths($months))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'total' => $item->total,
                    'completed' => $item->completed,
                    'completion_rate' => $item->total > 0
                        ? round(($item->completed / $item->total) * 100, 1)
                        : 0,
                ];
            });
    }

    /**
     * Get user registration trends.
     */
    public function getUserRegistrationTrends(int $months = 6): Collection
    {
        return User::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('COUNT(*) as total'),
            'role'
        )
            ->where('created_at', '>=', now()->subMonths($months))
            ->groupBy('month', 'role')
            ->orderBy('month')
            ->get()
            ->groupBy('month')
            ->map(function ($items, $month) {
                $result = ['month' => $month, 'total' => 0];
                foreach ($items as $item) {
                    $result[$item->role->value] = $item->total;
                    $result['total'] += $item->total;
                }

                return $result;
            })
            ->values();
    }

    /**
     * Get top performing paths.
     */
    public function getTopPaths(int $limit = 10): Collection
    {
        return LearningPath::query()
            ->with('creator')
            ->withCount('enrollments')
            ->withAvg('enrollments', 'progress_percent')
            ->where('is_published', true)
            ->orderBy('enrollments_count', 'desc')
            ->take($limit)
            ->get()
            ->map(function ($path) {
                $completedCount = $path->enrollments()
                    ->where('status', EnrollmentStatus::Completed)
                    ->count();

                return [
                    'id' => $path->id,
                    'title' => $path->title,
                    'creator' => $path->creator?->name,
                    'enrollments_count' => $path->enrollments_count,
                    'avg_progress' => round($path->enrollments_avg_progress_percent ?? 0),
                    'completion_rate' => $path->enrollments_count > 0
                        ? round(($completedCount / $path->enrollments_count) * 100, 1)
                        : 0,
                ];
            });
    }

    /**
     * Get instructor statistics.
     */
    public function getInstructorStats(User $instructor): array
    {
        $paths = $instructor->createdLearningPaths();
        $pathIds = $paths->pluck('id');

        $enrollments = Enrollment::whereIn('learning_path_id', $pathIds);
        $submissions = TaskSubmission::whereHas('task.step.module.learningPath', function ($query) use ($pathIds) {
            $query->whereIn('id', $pathIds);
        });

        return [
            'paths' => [
                'total' => $paths->count(),
                'published' => $paths->where('is_published', true)->count(),
            ],
            'enrollments' => [
                'total' => $enrollments->count(),
                'active' => (clone $enrollments)->where('status', EnrollmentStatus::Active)->count(),
                'completed' => (clone $enrollments)->where('status', EnrollmentStatus::Completed)->count(),
            ],
            'submissions' => [
                'total' => $submissions->count(),
                'pending' => (clone $submissions)->where('status', 'pending')->count(),
            ],
            'avg_completion_rate' => $this->calculateAvgCompletionRate($pathIds),
        ];
    }

    /**
     * Get learner statistics.
     */
    public function getLearnerStats(User $learner): array
    {
        $enrollments = $learner->enrollments();

        return [
            'enrollments' => [
                'total' => $enrollments->count(),
                'active' => (clone $enrollments)->where('status', EnrollmentStatus::Active)->count(),
                'completed' => (clone $enrollments)->where('status', EnrollmentStatus::Completed)->count(),
            ],
            'progress' => [
                'avg_progress' => round((clone $enrollments)->avg('progress_percent') ?? 0),
                'total_time_hours' => round((clone $enrollments)->sum('total_time_spent_seconds') / 3600, 1),
                'total_points' => (clone $enrollments)->sum('points_earned'),
            ],
            'certificates' => $learner->enrollments()
                ->whereHas('certificate')
                ->count(),
        ];
    }

    /**
     * Get team analytics.
     */
    public function getTeamStats(Team $team): array
    {
        $memberIds = $team->allUsers()->pluck('id');

        $enrollments = Enrollment::whereIn('user_id', $memberIds);

        return [
            'members' => [
                'total' => $memberIds->count(),
                'active_learners' => User::whereIn('id', $memberIds)
                    ->whereHas('enrollments', function ($q) {
                        $q->where('status', EnrollmentStatus::Active);
                    })->count(),
            ],
            'enrollments' => [
                'total' => $enrollments->count(),
                'active' => (clone $enrollments)->where('status', EnrollmentStatus::Active)->count(),
                'completed' => (clone $enrollments)->where('status', EnrollmentStatus::Completed)->count(),
            ],
            'progress' => [
                'avg_progress' => round((clone $enrollments)->avg('progress_percent') ?? 0),
                'total_time_hours' => round((clone $enrollments)->sum('total_time_spent_seconds') / 3600, 1),
            ],
            'completion_rate' => $enrollments->count() > 0
                ? round(((clone $enrollments)->where('status', EnrollmentStatus::Completed)->count() / $enrollments->count()) * 100, 1)
                : 0,
        ];
    }

    /**
     * Get path-specific analytics.
     */
    public function getPathAnalytics(LearningPath $path): array
    {
        $enrollments = $path->enrollments();

        return [
            'enrollments' => [
                'total' => $enrollments->count(),
                'active' => (clone $enrollments)->where('status', EnrollmentStatus::Active)->count(),
                'completed' => (clone $enrollments)->where('status', EnrollmentStatus::Completed)->count(),
                'paused' => (clone $enrollments)->where('status', EnrollmentStatus::Paused)->count(),
            ],
            'progress' => [
                'avg_progress' => round((clone $enrollments)->avg('progress_percent') ?? 0),
                'avg_time_hours' => round(((clone $enrollments)->avg('total_time_spent_seconds') ?? 0) / 3600, 1),
                'total_time_hours' => round((clone $enrollments)->sum('total_time_spent_seconds') / 3600, 1),
            ],
            'completion_rate' => $enrollments->count() > 0
                ? round(((clone $enrollments)->where('status', EnrollmentStatus::Completed)->count() / $enrollments->count()) * 100, 1)
                : 0,
            'avg_score' => $this->calculatePathAvgScore($path),
            'enrollments_by_month' => $this->getPathEnrollmentsByMonth($path),
        ];
    }

    /**
     * Get daily active users.
     */
    public function getDailyActiveUsers(int $days = 30): Collection
    {
        return Enrollment::select(
            DB::raw('DATE(last_activity_at) as date'),
            DB::raw('COUNT(DISTINCT user_id) as active_users')
        )
            ->where('last_activity_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Get learning time distribution.
     */
    public function getLearningTimeDistribution(): array
    {
        $data = Enrollment::select(
            DB::raw('HOUR(last_activity_at) as hour'),
            DB::raw('COUNT(*) as sessions')
        )
            ->whereNotNull('last_activity_at')
            ->where('last_activity_at', '>=', now()->subDays(30))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->pluck('sessions', 'hour')
            ->toArray();

        // Fill missing hours with 0
        $distribution = [];
        for ($i = 0; $i < 24; $i++) {
            $distribution[$i] = $data[$i] ?? 0;
        }

        return $distribution;
    }

    /**
     * Calculate average completion rate for paths.
     */
    protected function calculateAvgCompletionRate(Collection $pathIds): float
    {
        if ($pathIds->isEmpty()) {
            return 0;
        }

        $totalEnrollments = Enrollment::whereIn('learning_path_id', $pathIds)->count();
        $completedEnrollments = Enrollment::whereIn('learning_path_id', $pathIds)
            ->where('status', EnrollmentStatus::Completed)
            ->count();

        return $totalEnrollments > 0
            ? round(($completedEnrollments / $totalEnrollments) * 100, 1)
            : 0;
    }

    /**
     * Calculate average score for a path.
     */
    protected function calculatePathAvgScore(LearningPath $path): float
    {
        $completedEnrollments = $path->enrollments()
            ->where('status', EnrollmentStatus::Completed)
            ->get();

        if ($completedEnrollments->isEmpty()) {
            return 0;
        }

        $totalPossiblePoints = $path->modules()
            ->with('steps')
            ->get()
            ->flatMap(fn ($m) => $m->steps)
            ->sum('points_value');

        if ($totalPossiblePoints === 0) {
            return 0;
        }

        $avgPoints = $completedEnrollments->avg('points_earned');

        return round(($avgPoints / $totalPossiblePoints) * 100, 1);
    }

    /**
     * Get path enrollments grouped by month.
     */
    protected function getPathEnrollmentsByMonth(LearningPath $path, int $months = 6): Collection
    {
        return $path->enrollments()
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths($months))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month');
    }

    /**
     * Generate export data for reports.
     */
    public function generateReportData(string $type, array $filters = []): array
    {
        return match ($type) {
            'users' => $this->generateUsersReport($filters),
            'enrollments' => $this->generateEnrollmentsReport($filters),
            'paths' => $this->generatePathsReport($filters),
            default => [],
        };
    }

    /**
     * Generate users report data.
     */
    protected function generateUsersReport(array $filters): array
    {
        return User::query()
            ->when($filters['role'] ?? null, fn ($q, $role) => $q->where('role', $role))
            ->when($filters['from'] ?? null, fn ($q, $from) => $q->where('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($q, $to) => $q->where('created_at', '<=', $to))
            ->withCount('enrollments')
            ->get()
            ->map(fn ($user) => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->label(),
                'status' => $user->is_active ? __('Aktiv') : __('Inaktiv'),
                'enrollments' => $user->enrollments_count,
                'created_at' => $user->created_at->format('d.m.Y'),
            ])
            ->toArray();
    }

    /**
     * Generate enrollments report data.
     */
    protected function generateEnrollmentsReport(array $filters): array
    {
        return Enrollment::query()
            ->with(['user', 'learningPath'])
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($filters['path_id'] ?? null, fn ($q, $pathId) => $q->where('learning_path_id', $pathId))
            ->when($filters['from'] ?? null, fn ($q, $from) => $q->where('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($q, $to) => $q->where('created_at', '<=', $to))
            ->get()
            ->map(fn ($enrollment) => [
                'user' => $enrollment->user->name,
                'path' => $enrollment->learningPath->title,
                'status' => $enrollment->status->value,
                'progress' => $enrollment->progress_percent.'%',
                'points' => $enrollment->points_earned,
                'time_spent' => round($enrollment->total_time_spent_seconds / 3600, 1).'h',
                'started_at' => $enrollment->started_at?->format('d.m.Y'),
                'completed_at' => $enrollment->completed_at?->format('d.m.Y'),
            ])
            ->toArray();
    }

    /**
     * Generate paths report data.
     */
    protected function generatePathsReport(array $filters): array
    {
        return LearningPath::query()
            ->with('creator')
            ->withCount('enrollments')
            ->when(isset($filters['is_published']), fn ($q) => $q->where('is_published', $filters['is_published']))
            ->when($filters['from'] ?? null, fn ($q, $from) => $q->where('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($q, $to) => $q->where('created_at', '<=', $to))
            ->get()
            ->map(fn ($path) => [
                'title' => $path->title,
                'creator' => $path->creator?->name ?? __('Unbekannt'),
                'status' => $path->is_published ? __('Veröffentlicht') : __('Entwurf'),
                'difficulty' => $path->difficulty?->label(),
                'enrollments' => $path->enrollments_count,
                'created_at' => $path->created_at->format('d.m.Y'),
            ])
            ->toArray();
    }
}
