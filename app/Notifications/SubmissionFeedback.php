<?php

namespace App\Notifications;

use App\Models\TaskSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubmissionFeedback extends Notification implements ShouldQueue
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
        $isPassed = $this->submission->status === 'approved';

        $message = (new MailMessage)
            ->subject(__('Feedback zu deiner Abgabe: :task', ['task' => $task->title]))
            ->greeting(__('Hallo :name!', ['name' => $notifiable->name]))
            ->line(__('Deine Abgabe für **:task** wurde bewertet.', ['task' => $task->title]));

        if ($isPassed) {
            $message->line(__('**Ergebnis:** Bestanden'))
                ->line(__('**Punkte:** :score / :max', [
                    'score' => $this->submission->score ?? 0,
                    'max' => $task->max_points,
                ]));
        } else {
            $message->line(__('**Ergebnis:** Nicht bestanden'))
                ->line(__('Du kannst die Aufgabe erneut einreichen.'));
        }

        if ($this->submission->feedback) {
            $message->line(__('**Feedback vom Kursleiter:**'))
                ->line($this->submission->feedback);
        }

        return $message
            ->action(__('Zur Aufgabe'), url('/learn/task/'.$task->id))
            ->line(__('Bei Fragen wende dich an deinen Kursleiter.'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'submission_feedback',
            'title' => __('Abgabe bewertet'),
            'message' => __('Deine Abgabe für :task wurde bewertet.', [
                'task' => $this->submission->task->title,
            ]),
            'submission_id' => $this->submission->id,
            'task_id' => $this->submission->task_id,
            'status' => $this->submission->status,
            'score' => $this->submission->score,
        ];
    }
}
