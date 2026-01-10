<?php

namespace App\Policies;

use App\Models\StepProgress;
use App\Models\User;

class StepProgressPolicy
{
    /**
     * Determine whether the user can view any step progress.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the step progress.
     */
    public function view(User $user, StepProgress $progress): bool
    {
        // Owner can view their own progress
        if ($progress->enrollment->user_id === $user->id) {
            return true;
        }

        $path = $progress->step->module->learningPath;

        // Creator can view all progress
        if ($path->creator_id === $user->id) {
            return true;
        }

        // Admins can view all progress
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create step progress.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can start tracking progress on a step.
     */
    public function start(User $user, StepProgress $progress): bool
    {
        $path = $progress->step->module->learningPath;

        // Must be enrolled
        if (! $user->isEnrolledIn($path)) {
            return false;
        }

        // Must be their own progress
        if ($progress->enrollment->user_id !== $user->id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can update the step progress.
     */
    public function update(User $user, StepProgress $progress): bool
    {
        // Owner can update their own progress
        if ($progress->enrollment->user_id === $user->id) {
            return true;
        }

        $path = $progress->step->module->learningPath;

        // Creator can update (for manual adjustments)
        if ($path->creator_id === $user->id) {
            return true;
        }

        // Admins can update
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can complete the step.
     */
    public function complete(User $user, StepProgress $progress): bool
    {
        // Must be owner
        if ($progress->enrollment->user_id !== $user->id) {
            return false;
        }

        // Must be in progress
        if (! $progress->isInProgress()) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can reset the step progress.
     */
    public function reset(User $user, StepProgress $progress): bool
    {
        $path = $progress->step->module->learningPath;

        // Creator can reset
        if ($path->creator_id === $user->id) {
            return true;
        }

        // Admins can reset
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the step progress.
     */
    public function delete(User $user, StepProgress $progress): bool
    {
        $path = $progress->step->module->learningPath;

        // Creator can delete
        if ($path->creator_id === $user->id) {
            return true;
        }

        // Admins can delete
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view analytics for the step.
     */
    public function viewAnalytics(User $user, StepProgress $progress): bool
    {
        $path = $progress->step->module->learningPath;

        // Creator can view analytics
        if ($path->creator_id === $user->id) {
            return true;
        }

        // Admins can view analytics
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the step progress.
     */
    public function restore(User $user, StepProgress $progress): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the step progress.
     */
    public function forceDelete(User $user, StepProgress $progress): bool
    {
        return $user->isAdmin();
    }
}
