<?php

namespace App\Listeners;

use App\Events\StepCompleted;
use App\Events\SubmissionGraded;
use App\Notifications\SubmissionFeedback;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyLearnerOfGrade implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(SubmissionGraded $event): void
    {
        $submission = $event->submission;
        $reviewer = $event->reviewer;
        $enrollment = $submission->enrollment;
        $learner = $enrollment->user;

        // Notify the learner
        $learner->notify(new SubmissionFeedback($submission));

        // If submission is approved, mark step as completed
        if ($submission->status === 'approved') {
            $step = $submission->task->step;
            StepCompleted::dispatch($enrollment, $step);
        }

        // Log the grading
        activity()
            ->performedOn($submission)
            ->causedBy($reviewer)
            ->withProperties([
                'score' => $submission->score,
                'status' => $submission->status,
                'has_feedback' => !empty($submission->feedback),
            ])
            ->log('submission_graded');
    }
}
