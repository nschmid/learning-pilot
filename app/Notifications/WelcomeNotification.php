<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected ?string $temporaryPassword = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject(__('Willkommen bei :app', ['app' => config('app.name')]))
            ->greeting(__('Hallo :name!', ['name' => $notifiable->name]))
            ->line(__('Herzlich willkommen bei :app! Wir freuen uns, dich an Bord zu haben.', ['app' => config('app.name')]))
            ->line(__('Mit deinem neuen Konto kannst du:'))
            ->line('- '.__('Lernpfade entdecken und absolvieren'))
            ->line('- '.__('Deinen Fortschritt verfolgen'))
            ->line('- '.__('Zertifikate erwerben'));

        if ($this->temporaryPassword) {
            $message->line(__('Dein temporäres Passwort lautet: **:password**', ['password' => $this->temporaryPassword]))
                ->line(__('Bitte ändere dein Passwort nach dem ersten Login.'));
        }

        return $message
            ->action(__('Jetzt loslegen'), url('/dashboard'))
            ->line(__('Bei Fragen stehen wir dir gerne zur Verfügung.'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'welcome',
            'title' => __('Willkommen bei :app', ['app' => config('app.name')]),
            'message' => __('Dein Konto wurde erfolgreich erstellt.'),
        ];
    }
}
