<?php

namespace App\Listeners;

use App\Events\StepCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AwardPointsOnStepCompletion implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(StepCompleted $event): void
    {
        $enrollment = $event->enrollment;
        $step = $event->step;

        // Get points value from step
        $points = $step->points_value ?? config('lernpfad.gamification.points.step_completion', 10);

        // Update step progress with points
        $stepProgress = $enrollment->stepProgress()
            ->where('step_id', $step->id)
            ->first();

        if ($stepProgress && $stepProgress->points_earned === 0) {
            $stepProgress->update([
                'points_earned' => $points,
            ]);

            // Update total points on enrollment
            $totalPoints = $enrollment->stepProgress()->sum('points_earned');
            $enrollment->update([
                'points_earned' => $totalPoints,
            ]);
        }
    }
}
