<?php

namespace App\Notifications;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EnrollmentConfirmedNotification extends Notification implements ShouldQueue
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
            ->subject(__('Einschreibung bestätigt: :title', ['title' => $path->title]))
            ->greeting(__('Hallo :name!', ['name' => $notifiable->name]))
            ->line(__('Ihre Einschreibung für den Lernpfad ":title" wurde bestätigt.', ['title' => $path->title]))
            ->line(__('Sie können jetzt mit dem Lernen beginnen.'))
            ->action(__('Lernpfad starten'), route('learner.learn.index', $path->slug))
            ->line(__('Viel Erfolg beim Lernen!'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'enrollment_confirmed',
            'enrollment_id' => $this->enrollment->id,
            'learning_path_id' => $this->enrollment->learning_path_id,
            'learning_path_title' => $this->enrollment->learningPath->title,
        ];
    }
}
