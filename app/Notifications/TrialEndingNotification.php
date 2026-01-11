<?php

namespace App\Notifications;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrialEndingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Team $team,
        public int $daysRemaining
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Ihre Testphase endet in :days Tagen', ['days' => $this->daysRemaining]))
            ->greeting(__('Hallo :name!', ['name' => $notifiable->name]))
            ->line(__('Die kostenlose Testphase für ":team" endet in :days Tagen.', [
                'team' => $this->team->name,
                'days' => $this->daysRemaining,
            ]))
            ->line(__('Nach Ablauf der Testphase wird Ihr Konto pausiert und Sie können keine neuen Inhalte erstellen.'))
            ->line(__('Ihre bestehenden Daten bleiben erhalten.'))
            ->action(__('Jetzt Plan wählen'), route('billing.plans'))
            ->line(__('Wählen Sie einen Plan, der zu Ihren Bedürfnissen passt, um ohne Unterbrechung weiterzuarbeiten.'))
            ->line(__('Bei Fragen stehen wir Ihnen gerne zur Verfügung.'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'trial_ending',
            'team_id' => $this->team->id,
            'days_remaining' => $this->daysRemaining,
        ];
    }
}
