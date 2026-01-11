<?php

namespace App\Livewire\Instructor\Submissions;

use App\Models\TaskSubmission;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Show extends Component
{
    public TaskSubmission $submission;

    public function mount(TaskSubmission $submission): void
    {
        $this->submission = $submission->load([
            'task.step.module.learningPath',
            'enrollment.user',
            'reviewer',
        ]);

        // Verify ownership
        $learningPath = $this->submission->task->step->module->learningPath;
        if ($learningPath->creator_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403);
        }
    }

    public function review(): void
    {
        $this->redirect(route('instructor.submissions.review', $this->submission->id), navigate: true);
    }

    public function render()
    {
        return view('livewire.instructor.submissions.show')
            ->layout('layouts.instructor', ['title' => __('Einreichung')]);
    }
}
