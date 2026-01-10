<?php

namespace App\Livewire\Admin\Paths;

use App\Models\Enrollment;
use App\Models\LearningPath;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Show extends Component
{
    public LearningPath $path;

    public function mount(LearningPath $path): void
    {
        $this->path = $path->load(['creator', 'category', 'tags', 'modules.steps']);
    }

    #[Computed]
    public function stats(): array
    {
        $enrollments = Enrollment::where('learning_path_id', $this->path->id);

        return [
            'total_enrollments' => $enrollments->count(),
            'active_enrollments' => (clone $enrollments)->where('status', 'active')->count(),
            'completed_enrollments' => (clone $enrollments)->where('status', 'completed')->count(),
            'avg_progress' => round((clone $enrollments)->avg('progress_percent') ?? 0),
            'avg_time' => round(((clone $enrollments)->avg('total_time_spent_seconds') ?? 0) / 3600, 1),
        ];
    }

    #[Computed]
    public function recentEnrollments(): Collection
    {
        return Enrollment::with('user')
            ->where('learning_path_id', $this->path->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }

    #[Computed]
    public function moduleStats(): Collection
    {
        return $this->path->modules->map(function ($module) {
            return [
                'id' => $module->id,
                'title' => $module->title,
                'steps_count' => $module->steps->count(),
                'total_points' => $module->steps->sum('points_value'),
                'total_minutes' => $module->steps->sum('estimated_minutes'),
            ];
        });
    }

    public function togglePublished(): void
    {
        $this->path->update(['is_published' => ! $this->path->is_published]);
        $this->path->refresh();
    }

    public function render()
    {
        return view('livewire.admin.paths.show')
            ->layout('layouts.admin', ['title' => $this->path->title]);
    }
}
