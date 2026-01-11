<?php

namespace App\Livewire\Instructor\Students;

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
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
    public string $status = '';

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
    public function paths(): array
    {
        return Auth::user()->createdLearningPaths()
            ->select('id', 'title')
            ->orderBy('title')
            ->get()
            ->toArray();
    }

    #[Computed]
    public function enrollments()
    {
        $pathIds = Auth::user()->createdLearningPaths()->pluck('id');

        return Enrollment::query()
            ->with(['user', 'learningPath'])
            ->whereIn('learning_path_id', $pathIds)
            ->when($this->search, function ($q) {
                $q->whereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%"));
            })
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->pathId, fn ($q) => $q->where('learning_path_id', $this->pathId))
            ->orderByDesc('created_at')
            ->paginate(20);
    }

    #[Computed]
    public function statusCounts(): array
    {
        $pathIds = Auth::user()->createdLearningPaths()->pluck('id');

        return [
            'all' => Enrollment::whereIn('learning_path_id', $pathIds)->count(),
            'active' => Enrollment::whereIn('learning_path_id', $pathIds)->where('status', EnrollmentStatus::Active)->count(),
            'completed' => Enrollment::whereIn('learning_path_id', $pathIds)->where('status', EnrollmentStatus::Completed)->count(),
            'paused' => Enrollment::whereIn('learning_path_id', $pathIds)->where('status', EnrollmentStatus::Paused)->count(),
        ];
    }

    public function viewStudent(string $enrollmentId): void
    {
        $this->redirect(route('instructor.students.show', $enrollmentId), navigate: true);
    }

    public function render()
    {
        return view('livewire.instructor.students.index')
            ->layout('layouts.instructor', ['title' => __('Teilnehmer')]);
    }
}
