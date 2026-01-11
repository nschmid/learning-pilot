<?php

namespace App\Livewire\Instructor\Analytics;

use App\Services\AnalyticsService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Index extends Component
{
    public string $period = '30';

    #[Computed]
    public function stats(): array
    {
        $analyticsService = app(AnalyticsService::class);

        return $analyticsService->getInstructorStats(Auth::user());
    }

    #[Computed]
    public function topPaths(): array
    {
        $paths = Auth::user()->createdLearningPaths()
            ->withCount('enrollments')
            ->withAvg('enrollments', 'progress_percent')
            ->orderByDesc('enrollments_count')
            ->take(5)
            ->get();

        return $paths->map(fn ($path) => [
            'id' => $path->id,
            'title' => $path->title,
            'slug' => $path->slug,
            'enrollments' => $path->enrollments_count,
            'avg_progress' => round($path->enrollments_avg_progress_percent ?? 0),
            'is_published' => $path->is_published,
        ])->toArray();
    }

    #[Computed]
    public function recentEnrollments(): array
    {
        $pathIds = Auth::user()->createdLearningPaths()->pluck('id');

        return \App\Models\Enrollment::whereIn('learning_path_id', $pathIds)
            ->with(['user', 'learningPath'])
            ->orderByDesc('created_at')
            ->take(10)
            ->get()
            ->map(fn ($e) => [
                'user_name' => $e->user->name,
                'path_title' => $e->learningPath->title,
                'progress' => $e->progress_percent,
                'status' => $e->status->value,
                'created_at' => $e->created_at->diffForHumans(),
            ])->toArray();
    }

    #[Computed]
    public function pendingSubmissions(): int
    {
        $pathIds = Auth::user()->createdLearningPaths()->pluck('id');

        return \App\Models\TaskSubmission::whereHas('task.step.module.learningPath', function ($q) use ($pathIds) {
            $q->whereIn('id', $pathIds);
        })->where('status', 'pending')->count();
    }

    public function render()
    {
        return view('livewire.instructor.analytics.index')
            ->layout('layouts.instructor', ['title' => __('Analytik')]);
    }
}
