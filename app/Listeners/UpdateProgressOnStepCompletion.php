<?php

namespace App\Listeners;

use App\Events\PathCompleted;
use App\Events\StepCompleted;
use App\Services\ProgressTrackingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateProgressOnStepCompletion implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        protected ProgressTrackingService $progressService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(StepCompleted $event): void
    {
        $enrollment = $event->enrollment;

        // Recalculate overall progress
        $this->progressService->recalculateProgress($enrollment);

        // Check if path is now completed
        if ($enrollment->progress_percent >= 100) {
            // Dispatch path completed event
            PathCompleted::dispatch($enrollment);
        }
    }
}
