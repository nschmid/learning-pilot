<?php

namespace App\Listeners;

use App\Events\TaskSubmitted;
use App\Notifications\SubmissionReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyInstructorOfSubmission implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(TaskSubmitted $event): void
    {
        $submission = $event->submission;
        $task = $submission->task;
        $step = $task->step;
        $path = $step->module->learningPath;
        $instructor = $path->creator;

        // Notify the instructor
        $instructor->notify(new SubmissionReceived($submission));

        // Log the submission
        activity()
            ->performedOn($task)
            ->causedBy($submission->enrollment->user)
            ->withProperties([
                'submission_id' => $submission->id,
                'task_title' => $task->title,
            ])
            ->log('task_submitted');
    }
}
