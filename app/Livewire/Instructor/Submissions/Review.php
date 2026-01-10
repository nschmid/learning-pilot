<?php

namespace App\Livewire\Instructor\Submissions;

use App\Enums\SubmissionStatus;
use App\Models\TaskSubmission;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Review extends Component
{
    public TaskSubmission $submission;

    public int $score = 0;

    public string $feedback = '';

    public function mount(TaskSubmission $submission): void
    {
        $this->submission = $submission->load([
            'task.step.module.learningPath',
            'enrollment.user',
            'reviewer',
            'media',
        ]);

        // Verify ownership
        if ($this->submission->task->step->module->learningPath->creator_id !== Auth::id()) {
            abort(403);
        }

        // Pre-fill existing values
        $this->score = $this->submission->score ?? 0;
        $this->feedback = $this->submission->feedback ?? '';
    }

    public function approve(): void
    {
        $this->validate([
            'score' => ['required', 'integer', 'min:0', 'max:' . $this->submission->task->max_points],
            'feedback' => ['nullable', 'string', 'max:5000'],
        ]);

        $this->submission->update([
            'status' => SubmissionStatus::Reviewed,
            'score' => $this->score,
            'feedback' => $this->feedback,
            'reviewed_at' => now(),
            'reviewer_id' => Auth::id(),
        ]);

        session()->flash('success', __('Einreichung wurde bewertet.'));
        $this->redirect(route('instructor.submissions.index'), navigate: true);
    }

    public function requestRevision(): void
    {
        $this->validate([
            'feedback' => ['required', 'string', 'min:10', 'max:5000'],
        ], [
            'feedback.required' => __('Bitte gib ein Feedback für die Überarbeitung an.'),
            'feedback.min' => __('Das Feedback muss mindestens 10 Zeichen lang sein.'),
        ]);

        $this->submission->update([
            'status' => SubmissionStatus::RevisionRequested,
            'feedback' => $this->feedback,
            'reviewed_at' => now(),
            'reviewer_id' => Auth::id(),
        ]);

        session()->flash('success', __('Überarbeitung angefordert.'));
        $this->redirect(route('instructor.submissions.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.instructor.submissions.review')
            ->layout('layouts.instructor', ['title' => __('Einreichung bewerten')]);
    }
}
