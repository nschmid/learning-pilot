<?php

namespace App\Listeners;

use App\Events\AssessmentCompleted;
use App\Events\AssessmentFailed;
use App\Events\AssessmentPassed;
use App\Events\StepCompleted;
use App\Services\AssessmentGradingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProcessAssessmentCompletion implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        protected AssessmentGradingService $gradingService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(AssessmentCompleted $event): void
    {
        $attempt = $event->attempt;
        $enrollment = $attempt->enrollment;
        $assessment = $attempt->assessment;

        // Grade the assessment if not already graded
        if ($attempt->score_percent === null) {
            $this->gradingService->grade($attempt);
            $attempt->refresh();
        }

        // Dispatch passed or failed event
        if ($attempt->passed) {
            AssessmentPassed::dispatch($attempt);

            // If passed, mark the step as completed
            $step = $assessment->step;
            if ($step) {
                StepCompleted::dispatch($enrollment, $step);
            }
        } else {
            AssessmentFailed::dispatch($attempt);
        }

        // Log the attempt
        activity()
            ->performedOn($assessment)
            ->causedBy($enrollment->user)
            ->withProperties([
                'attempt_id' => $attempt->id,
                'score_percent' => $attempt->score_percent,
                'passed' => $attempt->passed,
                'time_spent' => $attempt->time_spent_seconds,
            ])
            ->log('assessment_completed');
    }
}
