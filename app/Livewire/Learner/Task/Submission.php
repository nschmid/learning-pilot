<?php

namespace App\Livewire\Learner\Task;

use App\Models\Task;
use App\Models\TaskSubmission;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Submission extends Component
{
    public Task $task;

    public TaskSubmission $submission;

    public function mount(Task $task, TaskSubmission $submission): void
    {
        $this->task = $task->load('step.module.learningPath');
        $this->submission = $submission->load(['task', 'reviewer', 'media']);

        // Verify this submission belongs to the user
        if ($this->submission->enrollment->user_id !== Auth::id()) {
            abort(403);
        }
    }

    public function render()
    {
        return view('livewire.learner.task.submission')
            ->layout('layouts.learner', ['title' => __('Einreichung')]);
    }
}
