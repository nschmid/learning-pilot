<?php

namespace App\Livewire\Instructor\Submissions;

use App\Enums\SubmissionStatus;
use App\Models\TaskSubmission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = 'pending';

    #[Url]
    public string $pathId = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedPathId(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function submissions(): LengthAwarePaginator
    {
        return TaskSubmission::whereHas('task.step.module.learningPath', function ($query) {
            $query->where('creator_id', Auth::id());
        })
            ->when($this->status, function ($query) {
                $query->where('status', SubmissionStatus::from($this->status));
            })
            ->when($this->pathId, function ($query) {
                $query->whereHas('task.step.module.learningPath', fn ($q) => $q->where('id', $this->pathId));
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('enrollment.user', fn ($uq) => $uq->where('name', 'like', "%{$this->search}%"))
                        ->orWhereHas('task', fn ($tq) => $tq->where('title', 'like', "%{$this->search}%"));
                });
            })
            ->with(['task', 'enrollment.user', 'task.step.module.learningPath'])
            ->orderBy('submitted_at', 'asc')
            ->paginate(20);
    }

    #[Computed]
    public function paths()
    {
        return \App\Models\LearningPath::where('creator_id', Auth::id())
            ->orderBy('title')
            ->get(['id', 'title']);
    }

    #[Computed]
    public function statusCounts(): array
    {
        $baseQuery = TaskSubmission::whereHas('task.step.module.learningPath', function ($query) {
            $query->where('creator_id', Auth::id());
        });

        return [
            'pending' => (clone $baseQuery)->where('status', SubmissionStatus::Pending)->count(),
            'reviewed' => (clone $baseQuery)->where('status', SubmissionStatus::Reviewed)->count(),
            'revision_requested' => (clone $baseQuery)->where('status', SubmissionStatus::RevisionRequested)->count(),
        ];
    }

    public function render()
    {
        return view('livewire.instructor.submissions.index')
            ->layout('layouts.instructor', ['title' => __('Einreichungen')]);
    }
}
