<?php

namespace App\Notifications;

use App\Models\TaskSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubmissionReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected TaskSubmission $submission
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $task = $this->submission->task;
        $learner = $this->submission->enrollment->user;
        $path = $task->step->module->learningPath;

        return (new MailMessage)
            ->subject(__('Neue Aufgaben-Abgabe: :task', ['task' => $task->title]))
            ->greeting(__('Hallo :name!', ['name' => $notifiable->name]))
            ->line(__('Eine neue Abgabe wartet auf deine Bewertung.'))
            ->line(__('**Lernpfad:** :path', ['path' => $path->title]))
            ->line(__('**Aufgabe:** :task', ['task' => $task->title]))
            ->line(__('**Eingereicht von:** :learner', ['learner' => $learner->name]))
            ->line(__('**Eingereicht am:** :date', ['date' => $this->submission->submitted_at->format('d.m.Y H:i')]))
            ->action(__('Abgabe bewerten'), url('/instructor/submissions/'.$this->submission->id.'/review'))
            ->line(__('Bitte bewerte die Abgabe zeitnah.'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'submission_received',
            'title' => __('Neue Abgabe erhalten'),
            'message' => __(':learner hat :task eingereicht.', [
                'learner' => $this->submission->enrollment->user->name,
                'task' => $this->submission->task->title,
            ]),
            'submission_id' => $this->submission->id,
            'task_id' => $this->submission->task_id,
        ];
    }
}
