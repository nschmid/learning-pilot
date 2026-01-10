<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\LearningPath;
use App\Models\TaskSubmission;
use App\Models\User;
use App\Notifications\EnrollmentConfirmation;
use App\Notifications\PathCompleted;
use App\Notifications\SubmissionFeedback;
use App\Notifications\SubmissionReceived;
use App\Notifications\WelcomeNotification;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    /**
     * Send welcome notification to new user.
     */
    public function sendWelcomeEmail(User $user, ?string $temporaryPassword = null): void
    {
        $user->notify(new WelcomeNotification($temporaryPassword));
    }

    /**
     * Send enrollment confirmation.
     */
    public function sendEnrollmentConfirmation(Enrollment $enrollment): void
    {
        $enrollment->user->notify(new EnrollmentConfirmation($enrollment));
    }

    /**
     * Send path completion notification with certificate.
     */
    public function sendPathCompletionNotification(Enrollment $enrollment, ?Certificate $certificate = null): void
    {
        $enrollment->user->notify(new PathCompleted($enrollment, $certificate));
    }

    /**
     * Notify instructor of new submission.
     */
    public function notifyInstructorOfSubmission(TaskSubmission $submission): void
    {
        $instructor = $submission->task->step->module->learningPath->creator;

        if ($instructor) {
            $instructor->notify(new SubmissionReceived($submission));
        }
    }

    /**
     * Send feedback notification to learner.
     */
    public function sendSubmissionFeedback(TaskSubmission $submission): void
    {
        $submission->enrollment->user->notify(new SubmissionFeedback($submission));
    }

    /**
     * Send bulk notification to multiple users.
     */
    public function sendBulkNotification(array $userIds, $notification): int
    {
        $users = User::whereIn('id', $userIds)->get();

        Notification::send($users, $notification);

        return $users->count();
    }

    /**
     * Send notification to all enrollees of a path.
     */
    public function notifyPathEnrollees(LearningPath $path, $notification): int
    {
        $users = User::whereHas('enrollments', function ($query) use ($path) {
            $query->where('learning_path_id', $path->id)
                ->where('status', 'active');
        })->get();

        Notification::send($users, $notification);

        return $users->count();
    }

    /**
     * Send notification to team members.
     */
    public function notifyTeamMembers($team, $notification, bool $includeOwner = true): int
    {
        $users = $team->allUsers();

        if (! $includeOwner) {
            $users = $users->filter(fn ($user) => $user->id !== $team->owner->id);
        }

        Notification::send($users, $notification);

        return $users->count();
    }
}
