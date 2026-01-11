<?php

namespace App\Notifications;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PathCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Enrollment $enrollment
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $path = $this->enrollment->learningPath;

        return (new MailMessage)
            ->subject(__('Herzlichen Glückwunsch! Lernpfad abgeschlossen'))
            ->greeting(__('Gratulation, :name!', ['name' => $notifiable->name]))
            ->line(__('Sie haben den Lernpfad ":title" erfolgreich abgeschlossen!', ['title' => $path->title]))
            ->line(__('Ihre Leistung:'))
            ->line('- ' . __('Punkte: :points', ['points' => $this->enrollment->points_earned]))
            ->line('- ' . __('Lernzeit: :time', ['time' => $this->formatDuration($this->enrollment->total_time_spent_seconds)]))
            ->when($this->enrollment->certificate, function ($mail) {
                return $mail->action(__('Zertifikat ansehen'), route('learner.certificates.show', $this->enrollment->certificate->id));
            }, function ($mail) use ($path) {
                return $mail->action(__('Weitere Lernpfade entdecken'), route('learner.catalog'));
            })
            ->line(__('Weiter so!'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'path_completed',
            'enrollment_id' => $this->enrollment->id,
            'learning_path_id' => $this->enrollment->learning_path_id,
            'learning_path_title' => $this->enrollment->learningPath->title,
            'points_earned' => $this->enrollment->points_earned,
            'completed_at' => $this->enrollment->completed_at?->toISOString(),
        ];
    }

    protected function formatDuration(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($hours > 0) {
            return __(':hours Std :minutes Min', ['hours' => $hours, 'minutes' => $minutes]);
        }

        return __(':minutes Min', ['minutes' => $minutes]);
    }
}
