<?php

namespace App\Livewire\Instructor\Analytics;

use App\Models\Enrollment;
use App\Models\LearningPath;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Paths extends Component
{
    #[Computed]
    public function paths(): array
    {
        $paths = Auth::user()->createdLearningPaths()
            ->withCount(['enrollments', 'modules', 'steps'])
            ->with('category')
            ->orderBy('title')
            ->get();

        return $paths->map(function ($path) {
            $completedEnrollments = Enrollment::where('learning_path_id', $path->id)
                ->where('status', 'completed')
                ->count();

            $avgProgress = Enrollment::where('learning_path_id', $path->id)
                ->avg('progress_percent') ?? 0;

            $avgTimeSpent = Enrollment::where('learning_path_id', $path->id)
                ->avg('total_time_spent_seconds') ?? 0;

            return [
                'id' => $path->id,
                'slug' => $path->slug,
                'title' => $path->title,
                'category' => $path->category?->name,
                'is_published' => $path->is_published,
                'modules_count' => $path->modules_count,
                'steps_count' => $path->steps_count,
                'enrollments_count' => $path->enrollments_count,
                'completed_count' => $completedEnrollments,
                'completion_rate' => $path->enrollments_count > 0
                    ? round(($completedEnrollments / $path->enrollments_count) * 100, 1)
                    : 0,
                'avg_progress' => round($avgProgress, 1),
                'avg_time_spent' => $this->formatTime((int) $avgTimeSpent),
            ];
        })->toArray();
    }

    #[Computed]
    public function totals(): array
    {
        $pathIds = Auth::user()->createdLearningPaths()->pluck('id');

        return [
            'paths' => count($this->paths),
            'published' => collect($this->paths)->where('is_published', true)->count(),
            'total_enrollments' => Enrollment::whereIn('learning_path_id', $pathIds)->count(),
            'total_completed' => Enrollment::whereIn('learning_path_id', $pathIds)->where('status', 'completed')->count(),
        ];
    }

    private function formatTime(int $seconds): string
    {
        if ($seconds === 0) {
            return '-';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$minutes}m";
    }

    public function render()
    {
        return view('livewire.instructor.analytics.paths')
            ->layout('layouts.instructor', ['title' => __('Lernpfad-Analytik')]);
    }
}
