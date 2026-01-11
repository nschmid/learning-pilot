<?php

namespace App\Livewire\Instructor\Analytics;

use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Students extends Component
{
    #[Computed]
    public function studentStats(): array
    {
        $pathIds = Auth::user()->createdLearningPaths()->pluck('id');

        // Get unique students with their aggregate stats
        $students = Enrollment::query()
            ->select([
                'user_id',
                DB::raw('COUNT(*) as enrollments_count'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_count'),
                DB::raw('AVG(progress_percent) as avg_progress'),
                DB::raw('SUM(total_time_spent_seconds) as total_time'),
                DB::raw('SUM(points_earned) as total_points'),
                DB::raw('MAX(last_activity_at) as last_active'),
            ])
            ->whereIn('learning_path_id', $pathIds)
            ->with('user')
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->limit(50)
            ->get();

        return $students->map(fn ($enrollment) => [
            'user_id' => $enrollment->user_id,
            'name' => $enrollment->user->name,
            'email' => $enrollment->user->email,
            'enrollments_count' => $enrollment->enrollments_count,
            'completed_count' => $enrollment->completed_count,
            'avg_progress' => round($enrollment->avg_progress ?? 0, 1),
            'total_time' => $this->formatTime((int) ($enrollment->total_time ?? 0)),
            'total_points' => $enrollment->total_points ?? 0,
            'last_active' => $enrollment->last_active
                ? \Carbon\Carbon::parse($enrollment->last_active)->format('d.m.Y')
                : '-',
        ])->toArray();
    }

    #[Computed]
    public function summary(): array
    {
        $pathIds = Auth::user()->createdLearningPaths()->pluck('id');

        $uniqueStudents = Enrollment::whereIn('learning_path_id', $pathIds)
            ->distinct('user_id')
            ->count('user_id');

        $activeThisWeek = Enrollment::whereIn('learning_path_id', $pathIds)
            ->where('last_activity_at', '>=', now()->subDays(7))
            ->distinct('user_id')
            ->count('user_id');

        $avgCompletionRate = Enrollment::whereIn('learning_path_id', $pathIds)
            ->selectRaw('AVG(CASE WHEN status = "completed" THEN 1 ELSE 0 END) * 100 as rate')
            ->value('rate') ?? 0;

        $totalTimeSpent = Enrollment::whereIn('learning_path_id', $pathIds)
            ->sum('total_time_spent_seconds');

        return [
            'unique_students' => $uniqueStudents,
            'active_this_week' => $activeThisWeek,
            'avg_completion_rate' => round($avgCompletionRate, 1),
            'total_learning_time' => $this->formatTime((int) $totalTimeSpent),
        ];
    }

    private function formatTime(int $seconds): string
    {
        if ($seconds === 0) {
            return '-';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($hours >= 24) {
            $days = floor($hours / 24);
            $hours = $hours % 24;

            return "{$days}d {$hours}h";
        }

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$minutes}m";
    }

    public function render()
    {
        return view('livewire.instructor.analytics.students')
            ->layout('layouts.instructor', ['title' => __('Teilnehmer-Analytik')]);
    }
}
