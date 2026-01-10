<?php

namespace App\Listeners;

use App\Events\EnrollmentCreated;
use App\Notifications\EnrollmentConfirmation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEnrollmentConfirmation implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(EnrollmentCreated $event): void
    {
        $enrollment = $event->enrollment;
        $user = $enrollment->user;
        $path = $enrollment->learningPath;

        // Send enrollment confirmation notification
        $user->notify(new EnrollmentConfirmation($enrollment));

        // Log the enrollment
        activity()
            ->performedOn($path)
            ->causedBy($user)
            ->withProperties([
                'enrollment_id' => $enrollment->id,
                'path_title' => $path->title,
            ])
            ->log('enrolled');
    }
}
