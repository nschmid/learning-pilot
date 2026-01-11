<?php

namespace App\Notifications;

use App\Models\TaskSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubmissionReviewedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public TaskSubmission $submission
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $task = $this->submission->task;
        $passed = $this->submission->score >= ($task->max_points * 0.6);

        $mail = (new MailMessage)
            ->subject(__('Ihre Aufgabe wurde bewertet'))
            ->greeting(__('Hallo :name!', ['name' => $notifiable->name]))
            ->line(__('Ihre Abgabe für die Aufgabe ":title" wurde bewertet.', ['title' => $task->title]));

        if ($this->submission->score !== null) {
            $mail->line(__('Punktzahl: :score / :max', [
                'score' => $this->submission->score,
                'max' => $task->max_points,
            ]));
        }

        if ($passed) {
            $mail->line(__('Herzlichen Glückwunsch! Sie haben die Aufgabe bestanden.'));
        }

        if ($this->submission->feedback) {
            $mail->line(__('Feedback des Dozenten:'))
                ->line($this->submission->feedback);
        }

        return $mail
            ->action(__('Bewertung ansehen'), route('learner.task.submission', [$task->id, $this->submission->id]))
            ->line(__('Bei Fragen wenden Sie sich an Ihren Dozenten.'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'submission_reviewed',
            'submission_id' => $this->submission->id,
            'task_id' => $this->submission->task_id,
            'task_title' => $this->submission->task->title,
            'score' => $this->submission->score,
            'max_score' => $this->submission->task->max_points,
            'reviewed_at' => $this->submission->reviewed_at?->toISOString(),
        ];
    }
}
