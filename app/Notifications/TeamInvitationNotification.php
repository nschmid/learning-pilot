<?php

namespace App\Notifications;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Team $team,
        protected ?string $temporaryPassword = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject(__('Einladung zum Team :team', ['team' => $this->team->name]))
            ->greeting(__('Hallo :name!', ['name' => $notifiable->name]))
            ->line(__('Sie wurden zum Team ":team" bei :app eingeladen.', [
                'team' => $this->team->name,
                'app' => config('app.name'),
            ]));

        if ($this->temporaryPassword) {
            $message->line(__('Ein Konto wurde für Sie erstellt.'))
                ->line(__('Ihr temporäres Passwort lautet: **:password**', ['password' => $this->temporaryPassword]))
                ->line(__('Bitte ändern Sie Ihr Passwort nach dem ersten Login.'));
        } else {
            $message->line(__('Sie können sich mit Ihrem bestehenden Konto anmelden.'));
        }

        return $message
            ->action(__('Zum Dashboard'), url('/dashboard'))
            ->line(__('Bei Fragen stehen wir Ihnen gerne zur Verfügung.'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'team_invitation',
            'title' => __('Team-Einladung'),
            'message' => __('Sie wurden zum Team ":team" eingeladen.', ['team' => $this->team->name]),
            'team_id' => $this->team->id,
            'team_name' => $this->team->name,
        ];
    }
}
