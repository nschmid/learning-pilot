<?php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateIssuedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Certificate $certificate
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $path = $this->certificate->enrollment->learningPath;

        return (new MailMessage)
            ->subject(__('Ihr Zertifikat ist bereit!'))
            ->greeting(__('Hallo :name!', ['name' => $notifiable->name]))
            ->line(__('Ihr Zertifikat für den Lernpfad ":title" wurde ausgestellt.', ['title' => $path->title]))
            ->line(__('Zertifikatsnummer: :number', ['number' => $this->certificate->certificate_number]))
            ->when($this->certificate->expires_at, function ($mail) {
                return $mail->line(__('Gültig bis: :date', ['date' => $this->certificate->expires_at->format('d.m.Y')]));
            })
            ->action(__('Zertifikat herunterladen'), route('learner.certificates.show', $this->certificate->id))
            ->line(__('Sie können Ihr Zertifikat auch über den folgenden Link verifizieren:'))
            ->line(route('certificate.verify', $this->certificate->certificate_number));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'certificate_issued',
            'certificate_id' => $this->certificate->id,
            'certificate_number' => $this->certificate->certificate_number,
            'learning_path_title' => $this->certificate->enrollment->learningPath->title,
            'issued_at' => $this->certificate->issued_at->toISOString(),
        ];
    }
}
