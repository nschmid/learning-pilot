<?php

namespace App\Livewire\Learner\Path;

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\LearningPath;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Show extends Component
{
    public LearningPath $path;

    public bool $showCurriculum = true;

    public function mount(LearningPath $path): void
    {
        // Ensure path is published or user is creator
        if (! $path->is_published && $path->creator_id !== Auth::id()) {
            abort(404);
        }

        $this->path = $path->load([
            'creator',
            'category',
            'tags',
            'modules.steps',
            'reviews' => fn ($q) => $q->where('is_approved', true)->latest()->limit(5),
            'reviews.user',
        ]);
    }

    public function getEnrollmentProperty(): ?Enrollment
    {
        if (! Auth::check()) {
            return null;
        }

        return Enrollment::where('user_id', Auth::id())
            ->where('learning_path_id', $this->path->id)
            ->first();
    }

    public function getIsEnrolledProperty(): bool
    {
        return $this->enrollment !== null;
    }

    public function getStatsProperty(): array
    {
        return [
            'modules' => $this->path->modules->count(),
            'steps' => $this->path->modules->sum(fn ($m) => $m->steps->count()),
            'duration' => $this->path->estimated_hours ?? round($this->path->modules->sum(fn ($m) => $m->steps->sum('estimated_minutes')) / 60, 1),
            'points' => $this->path->modules->sum(fn ($m) => $m->steps->sum('points_value')),
            'enrollments' => $this->path->enrollments()->count(),
            'completions' => $this->path->enrollments()->where('status', EnrollmentStatus::Completed)->count(),
            'rating' => $this->path->reviews()->where('is_approved', true)->avg('rating') ?? 0,
            'reviews_count' => $this->path->reviews()->where('is_approved', true)->count(),
        ];
    }

    public function enroll(): void
    {
        if (! Auth::check()) {
            $this->redirect(route('login'));

            return;
        }

        if ($this->isEnrolled) {
            return;
        }

        Enrollment::create([
            'user_id' => Auth::id(),
            'learning_path_id' => $this->path->id,
            'status' => EnrollmentStatus::Active,
            'progress_percent' => 0,
            'started_at' => now(),
            'last_activity_at' => now(),
            'total_time_spent_seconds' => 0,
            'points_earned' => 0,
        ]);

        $this->dispatch('enrolled');

        // Redirect to learning interface
        $this->redirect(
            route('learner.learn.index', $this->path->slug),
            navigate: true
        );
    }

    public function continueLearning(): void
    {
        $this->redirect(
            route('learner.learn.index', $this->path->slug),
            navigate: true
        );
    }

    public function render()
    {
        return view('livewire.learner.path.show')
            ->layout('layouts.learner', ['title' => $this->path->title]);
    }
}
