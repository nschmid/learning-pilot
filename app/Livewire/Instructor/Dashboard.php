<?php

namespace App\Livewire\Instructor;

use App\Enums\SubmissionStatus;
use App\Models\Enrollment;
use App\Models\LearningPath;
use App\Models\TaskSubmission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Dashboard extends Component
{
    #[Computed]
    public function paths(): Collection
    {
        return LearningPath::where('creator_id', Auth::id())
            ->withCount(['enrollments', 'modules', 'steps'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();
    }

    #[Computed]
    public function totalPaths(): int
    {
        return LearningPath::where('creator_id', Auth::id())->count();
    }

    #[Computed]
    public function publishedPaths(): int
    {
        return LearningPath::where('creator_id', Auth::id())
            ->where('is_published', true)
            ->count();
    }

    #[Computed]
    public function totalEnrollments(): int
    {
        return Enrollment::whereHas('learningPath', function ($query) {
            $query->where('creator_id', Auth::id());
        })->count();
    }

    #[Computed]
    public function activeStudents(): int
    {
        return Enrollment::whereHas('learningPath', function ($query) {
            $query->where('creator_id', Auth::id());
        })
            ->where('last_activity_at', '>=', now()->subDays(7))
            ->distinct('user_id')
            ->count('user_id');
    }

    #[Computed]
    public function pendingSubmissions(): Collection
    {
        return TaskSubmission::whereHas('task.step.module.learningPath', function ($query) {
            $query->where('creator_id', Auth::id());
        })
            ->where('status', SubmissionStatus::Pending)
            ->with(['task', 'enrollment.user'])
            ->orderBy('submitted_at', 'asc')
            ->take(5)
            ->get();
    }

    #[Computed]
    public function pendingSubmissionsCount(): int
    {
        return TaskSubmission::whereHas('task.step.module.learningPath', function ($query) {
            $query->where('creator_id', Auth::id());
        })
            ->where('status', SubmissionStatus::Pending)
            ->count();
    }

    #[Computed]
    public function recentCompletions(): Collection
    {
        return Enrollment::whereHas('learningPath', function ($query) {
            $query->where('creator_id', Auth::id());
        })
            ->whereNotNull('completed_at')
            ->with(['user', 'learningPath'])
            ->orderBy('completed_at', 'desc')
            ->take(5)
            ->get();
    }

    #[Computed]
    public function averageCompletion(): float
    {
        $avg = Enrollment::whereHas('learningPath', function ($query) {
            $query->where('creator_id', Auth::id());
        })->avg('progress_percent');

        return round($avg ?? 0, 1);
    }

    public function render()
    {
        return view('livewire.instructor.dashboard')
            ->layout('layouts.instructor', ['title' => __('Dashboard')]);
    }
}
