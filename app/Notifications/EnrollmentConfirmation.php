<?php

namespace App\Notifications;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EnrollmentConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Enrollment $enrollment
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
            ->line(__('Du hast dich erfolgreich für den Lernpfad **:title** eingeschrieben.', ['title' => $path->title]))
            ->line(__('Hier sind einige Details zu deinem Kurs:'))
            ->line('- '.__('Schwierigkeit: :difficulty', ['difficulty' => $path->difficulty?->label() ?? __('Nicht angegeben')]))
            ->line('- '.__('Geschätzte Dauer: :hours Stunden', ['hours' => $path->estimated_hours ?? __('Nicht angegeben')]))
            ->line('- '.__('Module: :count', ['count' => $path->modules()->count()]))
            ->action(__('Jetzt starten'), url('/learn/path/'.$path->slug.'/learn'))
            ->line(__('Viel Erfolg beim Lernen!'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'enrollment_confirmed',
            'title' => __('Einschreibung bestätigt'),
            'message' => __('Du bist jetzt für :title eingeschrieben.', [
                'title' => $this->enrollment->learningPath->title,
            ]),
            'enrollment_id' => $this->enrollment->id,
            'learning_path_id' => $this->enrollment->learning_path_id,
        ];
    }
}
