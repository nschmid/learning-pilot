<?php

namespace App\Livewire\Learner\Task;

use App\Enums\SubmissionStatus;
use App\Models\Enrollment;
use App\Models\Task;
use App\Models\TaskSubmission;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Show extends Component
{
    use WithFileUploads;

    public Task $task;

    public string $content = '';

    public array $files = [];

    public bool $showSubmitForm = false;

    public function mount(Task $task): void
    {
        $this->task = $task->load('step.module.learningPath');
    }

    #[Computed]
    public function enrollment(): ?Enrollment
    {
        $path = $this->task->step->module->learningPath;

        return Enrollment::where('user_id', Auth::id())
            ->where('learning_path_id', $path->id)
            ->first();
    }

    #[Computed]
    public function submissions()
    {
        if (! $this->enrollment) {
            return collect();
        }

        return TaskSubmission::where('task_id', $this->task->id)
            ->where('enrollment_id', $this->enrollment->id)
            ->orderBy('submitted_at', 'desc')
            ->get();
    }

    #[Computed]
    public function latestSubmission(): ?TaskSubmission
    {
        return $this->submissions->first();
    }

    #[Computed]
    public function canSubmit(): bool
    {
        if (! $this->enrollment) {
            return false;
        }

        // Check if resubmission is allowed
        if ($this->latestSubmission) {
            if (! $this->task->allow_resubmit) {
                return false;
            }

            // Can only resubmit if status is RevisionRequested
            if ($this->latestSubmission->status !== SubmissionStatus::RevisionRequested) {
                return false;
            }
        }

        return true;
    }

    #[Computed]
    public function hasPassed(): bool
    {
        if (! $this->latestSubmission || ! $this->latestSubmission->isReviewed()) {
            return false;
        }

        // Consider passed if score is >= 60%
        return $this->latestSubmission->scorePercent() >= 60;
    }

    #[Computed]
    public function stepProgressId(): ?string
    {
        if (! $this->enrollment) {
            return null;
        }

        $stepProgress = $this->enrollment->stepProgress()
            ->where('step_id', $this->task->step_id)
            ->first();

        return $stepProgress?->id;
    }

    public function toggleSubmitForm(): void
    {
        $this->showSubmitForm = ! $this->showSubmitForm;
    }

    public function submit(): void
    {
        if (! $this->canSubmit) {
            return;
        }

        $this->validate([
            'content' => 'required|min:10',
            'files.*' => 'nullable|file|max:' . (($this->task->max_file_size_mb ?? 10) * 1024),
        ], [
            'content.required' => __('Bitte gib eine Antwort ein.'),
            'content.min' => __('Deine Antwort muss mindestens 10 Zeichen lang sein.'),
        ]);

        $submission = TaskSubmission::create([
            'task_id' => $this->task->id,
            'enrollment_id' => $this->enrollment->id,
            'content' => $this->content,
            'status' => SubmissionStatus::Pending,
            'submitted_at' => now(),
        ]);

        // Handle file uploads
        if (! empty($this->files)) {
            foreach ($this->files as $file) {
                $submission->addMedia($file->getRealPath())
                    ->usingFileName($file->getClientOriginalName())
                    ->toMediaCollection('submissions');
            }
        }

        $this->content = '';
        $this->files = [];
        $this->showSubmitForm = false;

        // Clear computed properties cache
        unset($this->submissions);
        unset($this->latestSubmission);
        unset($this->canSubmit);

        $this->dispatch('submission-created');
    }

    public function viewSubmission(string $submissionId): void
    {
        $this->redirect(
            route('learner.task.submission', [$this->task->id, $submissionId]),
            navigate: true
        );
    }

    public function render()
    {
        return view('livewire.learner.task.show')
            ->layout('layouts.learner', ['title' => $this->task->title]);
    }
}
