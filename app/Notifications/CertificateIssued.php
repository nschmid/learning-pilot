<?php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateIssued extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Certificate $certificate
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $path = $this->certificate->enrollment->learningPath;

        return (new MailMessage)
            ->subject(__('Herzlichen Glückwunsch! Dein Zertifikat ist bereit'))
            ->greeting(__('Hallo :name!', ['name' => $notifiable->name]))
            ->line(__('Du hast den Lernpfad ":path" erfolgreich abgeschlossen!', [
                'path' => $path->title,
            ]))
            ->line(__('Dein Zertifikat mit der Nummer :number wurde ausgestellt.', [
                'number' => $this->certificate->certificate_number,
            ]))
            ->action(__('Zertifikat anzeigen'), route('learner.certificates.show', $this->certificate))
            ->line(__('Teile deine Leistung mit anderen oder lade das Zertifikat als PDF herunter.'));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $path = $this->certificate->enrollment->learningPath;

        return [
            'type' => 'certificate_issued',
            'certificate_id' => $this->certificate->id,
            'certificate_number' => $this->certificate->certificate_number,
            'path_id' => $path->id,
            'path_title' => $path->title,
            'issued_at' => $this->certificate->issued_at->toISOString(),
        ];
    }
}
