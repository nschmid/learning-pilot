<?php

namespace App\Listeners;

use App\Events\CertificateIssued;
use App\Notifications\CertificateIssued as CertificateIssuedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendCertificateNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(CertificateIssued $event): void
    {
        $certificate = $event->certificate;
        $enrollment = $certificate->enrollment;
        $user = $enrollment->user;
        $path = $enrollment->learningPath;

        // Send certificate notification
        $user->notify(new CertificateIssuedNotification($certificate));

        // Log certificate issuance
        activity()
            ->performedOn($certificate)
            ->causedBy($user)
            ->withProperties([
                'certificate_number' => $certificate->certificate_number,
                'path_title' => $path->title,
                'issued_at' => $certificate->issued_at,
            ])
            ->log('certificate_issued');
    }
}
