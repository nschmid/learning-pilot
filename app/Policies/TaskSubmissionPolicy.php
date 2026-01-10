<?php

namespace App\Policies;

use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\TaskSubmission;
use App\Models\User;

class TaskSubmissionPolicy
{
    /**
     * Determine whether the user can view any task submissions.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [UserRole::Admin, UserRole::Instructor], true);
    }

    /**
     * Determine whether the user can view the task submission.
     */
    public function view(User $user, TaskSubmission $submission): bool
    {
        // Admin can view all
        if ($user->role === UserRole::Admin) {
            return true;
        }

        // User can view their own submission
        if ($user->id === $submission->enrollment->user_id) {
            return true;
        }

        // Instructor can view submissions in their paths
        if ($user->role === UserRole::Instructor) {
            return $submission->task->step->module->learningPath->creator_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create task submissions.
     */
    public function create(User $user): bool
    {
        return true; // Learners can submit tasks
    }

    /**
     * Determine whether the user can update the task submission.
     */
    public function update(User $user, TaskSubmission $submission): bool
    {
        // Can only update pending submissions
        if ($submission->status !== SubmissionStatus::Pending) {
            return false;
        }

        // User can update their own pending submission
        return $user->id === $submission->enrollment->user_id;
    }

    /**
     * Determine whether the user can delete the task submission.
     */
    public function delete(User $user, TaskSubmission $submission): bool
    {
        // Admin can delete all
        if ($user->role === UserRole::Admin) {
            return true;
        }

        // User can delete their own pending submission
        if ($submission->status === SubmissionStatus::Pending) {
            return $user->id === $submission->enrollment->user_id;
        }

        return false;
    }

    /**
     * Determine whether the user can review the task submission.
     */
    public function review(User $user, TaskSubmission $submission): bool
    {
        // Admin can review all
        if ($user->role === UserRole::Admin) {
            return true;
        }

        // Instructor can review submissions in their paths
        if ($user->role === UserRole::Instructor) {
            return $submission->task->step->module->learningPath->creator_id === $user->id;
        }

        return false;
    }
}
