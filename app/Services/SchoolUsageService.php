<?php

namespace App\Services;

use App\Models\Team;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Models\Activity;

class SchoolUsageService
{
    /**
     * Get comprehensive usage statistics for a team.
     */
    public function getUsageStats(Team $team): array
    {
        $subscriptionService = app(SubscriptionService::class);
        $plan = $subscriptionService->getCurrentPlan($team);

        return [
            'students' => $this->getStudentStats($team, $plan),
            'instructors' => $this->getInstructorStats($team, $plan),
            'storage' => $this->getStorageStats($team, $plan),
            'ai_requests' => $this->getAIStats($team, $plan),
            'paths' => $this->getPathStats($team),
            'enrollments' => $this->getEnrollmentStats($team),
        ];
    }

    protected function getStudentStats(Team $team, ?array $plan): array
    {
        $count = $team->users()
            ->whereHas('roles', fn ($q) => $q->where('name', 'learner'))
            ->count();

        $limit = $plan['limits']['students'] ?? 50;

        return [
            'current' => $count,
            'limit' => $limit,
            'percent' => $limit > 0 ? round(($count / $limit) * 100, 1) : 0,
            'remaining' => max(0, $limit - $count),
        ];
    }

    protected function getInstructorStats(Team $team, ?array $plan): array
    {
        $count = $team->users()
            ->whereHas('roles', fn ($q) => $q->where('name', 'instructor'))
            ->count();

        $limit = $plan['limits']['instructors'] ?? 3;

        return [
            'current' => $count,
            'limit' => $limit,
            'percent' => $limit > 0 ? round(($count / $limit) * 100, 1) : 0,
            'remaining' => max(0, $limit - $count),
        ];
    }

    protected function getStorageStats(Team $team, ?array $plan): array
    {
        $usedBytes = Cache::remember(
            "team.{$team->id}.storage_used",
            now()->addMinutes(15),
            fn () => $this->calculateStorageUsed($team)
        );

        $limitGb = $plan['limits']['storage_gb'] ?? 5;
        $limitBytes = $limitGb * 1024 * 1024 * 1024;

        return [
            'used_bytes' => $usedBytes,
            'limit_bytes' => $limitBytes,
            'used_formatted' => $this->formatBytes($usedBytes),
            'limit_formatted' => "{$limitGb} GB",
            'percent' => $limitBytes > 0 ? round(($usedBytes / $limitBytes) * 100, 1) : 0,
        ];
    }

    protected function getAIStats(Team $team, ?array $plan): array
    {
        $todayCount = \App\Models\AiUsageLog::where('team_id', $team->id)
            ->whereDate('created_at', today())
            ->count();

        $monthCount = \App\Models\AiUsageLog::where('team_id', $team->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $dailyLimit = $plan['limits']['ai_daily_requests'] ?? 100;

        return [
            'today' => $todayCount,
            'daily_limit' => $dailyLimit,
            'month' => $monthCount,
            'daily_percent' => $dailyLimit > 0 ? round(($todayCount / $dailyLimit) * 100, 1) : 0,
        ];
    }

    protected function getPathStats(Team $team): array
    {
        $paths = $team->learningPaths();

        return [
            'total' => $paths->count(),
            'published' => $paths->where('is_published', true)->count(),
            'draft' => $paths->where('is_published', false)->count(),
        ];
    }

    protected function getEnrollmentStats(Team $team): array
    {
        $enrollments = \App\Models\Enrollment::whereHas('learningPath', function ($q) use ($team) {
            $q->where('team_id', $team->id);
        });

        $total = $enrollments->count();
        $completed = (clone $enrollments)->where('status', 'completed')->count();
        $active = (clone $enrollments)->where('status', 'active')->count();

        return [
            'total' => $total,
            'completed' => $completed,
            'active' => $active,
            'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 1) : 0,
        ];
    }

    protected function calculateStorageUsed(Team $team): int
    {
        return $team->learningPaths()
            ->with('modules.steps.materials')
            ->get()
            ->flatMap(fn ($path) => $path->modules)
            ->flatMap(fn ($module) => $module->steps)
            ->flatMap(fn ($step) => $step->materials)
            ->sum('file_size');
    }

    protected function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }

    /**
     * Get activity timeline for the team.
     */
    public function getRecentActivity(Team $team, int $limit = 10): array
    {
        // Get team member IDs
        $teamUserIds = $team->users()->pluck('users.id')->toArray();

        // Get learning path IDs for this team
        $pathIds = $team->learningPaths()->pluck('id')->toArray();

        // Query activities related to team members or team's learning paths
        $activities = Activity::query()
            ->where(function ($query) use ($teamUserIds, $pathIds) {
                // Activities by team members
                $query->whereIn('causer_id', $teamUserIds)
                    ->where('causer_type', 'App\\Models\\User');
            })
            ->orWhere(function ($query) use ($pathIds) {
                // Activities on team's learning paths
                $query->whereIn('subject_id', $pathIds)
                    ->where('subject_type', 'App\\Models\\LearningPath');
            })
            ->with(['causer:id,name,email', 'subject'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return $activities->map(function ($activity) {
            return [
                'id' => $activity->id,
                'description' => $activity->description,
                'event' => $activity->event,
                'subject_type' => $activity->subject_type ? class_basename($activity->subject_type) : null,
                'subject_name' => $this->getSubjectName($activity),
                'causer_name' => $activity->causer?->name ?? __('System'),
                'properties' => $activity->properties->toArray(),
                'created_at' => $activity->created_at->toISOString(),
                'created_at_human' => $activity->created_at->diffForHumans(),
                'icon' => $this->getActivityIcon($activity),
            ];
        })->toArray();
    }

    /**
     * Get a human-readable name for the activity subject.
     */
    protected function getSubjectName(Activity $activity): ?string
    {
        if (!$activity->subject) {
            return $activity->properties['subject_name'] ?? null;
        }

        // Try common name attributes
        return $activity->subject->title
            ?? $activity->subject->name
            ?? $activity->subject->certificate_number
            ?? class_basename($activity->subject_type) . ' #' . $activity->subject_id;
    }

    /**
     * Get an icon identifier for the activity type.
     */
    protected function getActivityIcon(Activity $activity): string
    {
        $subjectType = $activity->subject_type ? class_basename($activity->subject_type) : null;
        $event = $activity->event;

        return match (true) {
            $subjectType === 'LearningPath' && $event === 'created' => 'path-created',
            $subjectType === 'LearningPath' && $event === 'published' => 'path-published',
            $subjectType === 'LearningPath' && $event === 'duplicated' => 'path-duplicated',
            $subjectType === 'Enrollment' && $event === 'created' => 'enrollment-created',
            $subjectType === 'Enrollment' && $event === 'completed' => 'enrollment-completed',
            $subjectType === 'Certificate' => 'certificate',
            $subjectType === 'AssessmentAttempt' => 'assessment',
            $subjectType === 'TaskSubmission' => 'task',
            default => 'activity',
        };
    }
}
