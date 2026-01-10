<?php

namespace App\Listeners;

use App\Events\CertificateIssued;
use App\Events\PathCompleted;
use App\Notifications\PathCompleted as PathCompletedNotification;
use App\Services\CertificateGeneratorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandlePathCompletion implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        protected CertificateGeneratorService $certificateService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(PathCompleted $event): void
    {
        $enrollment = $event->enrollment;
        $user = $enrollment->user;
        $path = $enrollment->learningPath;

        // Mark enrollment as completed
        $enrollment->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Send completion notification
        $user->notify(new PathCompletedNotification($enrollment));

        // Generate certificate if path awards one
        if ($path->awards_certificate ?? true) {
            $certificate = $this->certificateService->generate($enrollment);

            if ($certificate) {
                CertificateIssued::dispatch($certificate);
            }
        }

        // Log completion
        activity()
            ->performedOn($path)
            ->causedBy($user)
            ->withProperties([
                'enrollment_id' => $enrollment->id,
                'completion_time' => $enrollment->total_time_spent_seconds,
                'points_earned' => $enrollment->points_earned,
            ])
            ->log('completed');
    }
}
