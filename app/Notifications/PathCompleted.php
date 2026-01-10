<?php

namespace App\Notifications;

use App\Models\Certificate;
use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PathCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Enrollment $enrollment,
        protected ?Certificate $certificate = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $path = $this->enrollment->learningPath;

        $message = (new MailMessage)
            ->subject(__('Herzlichen Glückwunsch! :title abgeschlossen', ['title' => $path->title]))
            ->greeting(__('Gratulation, :name!', ['name' => $notifiable->name]))
            ->line(__('Du hast den Lernpfad **:title** erfolgreich abgeschlossen!', ['title' => $path->title]))
            ->line(__('Deine Leistung:'))
            ->line('- '.__('Fortschritt: 100%'))
            ->line('- '.__('Punkte: :points', ['points' => number_format($this->enrollment->points_earned)]))
            ->line('- '.__('Lernzeit: :hours Stunden', ['hours' => round($this->enrollment->total_time_spent_seconds / 3600, 1)]));

        if ($this->certificate) {
            $message->line(__('Dein Zertifikat ist bereit!'))
                ->line(__('Zertifikatsnummer: :number', ['number' => $this->certificate->certificate_number]))
                ->action(__('Zertifikat herunterladen'), url('/learn/certificates/'.$this->certificate->id));
        } else {
            $message->action(__('Zum Dashboard'), url('/dashboard'));
        }

        return $message->line(__('Weiter so! Entdecke weitere Lernpfade.'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'path_completed',
            'title' => __('Lernpfad abgeschlossen'),
            'message' => __('Du hast :title erfolgreich abgeschlossen!', [
                'title' => $this->enrollment->learningPath->title,
            ]),
            'enrollment_id' => $this->enrollment->id,
            'learning_path_id' => $this->enrollment->learning_path_id,
            'certificate_id' => $this->certificate?->id,
        ];
    }
}
