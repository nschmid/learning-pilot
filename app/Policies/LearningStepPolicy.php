<?php

namespace App\Policies;

use App\Models\LearningStep;
use App\Models\User;

class LearningStepPolicy
{
    /**
     * Determine whether the user can view any steps.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the step.
     */
    public function view(User $user, LearningStep $step): bool
    {
        $path = $step->module->learningPath;

        // Preview steps are always viewable if path is published
        if ($path->is_published && $step->is_previewable) {
            return true;
        }

        // Creator can view all steps
        if ($path->creator_id === $user->id) {
            return true;
        }

        // Enrolled users can view steps
        if ($user->isEnrolledIn($path)) {
            return true;
        }

        // Admins can view all steps
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create steps.
     */
    public function create(User $user): bool
    {
        return $user->isInstructor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the step.
     */
    public function update(User $user, LearningStep $step): bool
    {
        $path = $step->module->learningPath;

        // Creator can update
        if ($path->creator_id === $user->id) {
            return true;
        }

        // Admins can update
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the step.
     */
    public function delete(User $user, LearningStep $step): bool
    {
        $path = $step->module->learningPath;

        // Creator can delete
        if ($path->creator_id === $user->id) {
            return true;
        }

        // Admins can delete
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can access the step content.
     */
    public function access(User $user, LearningStep $step): bool
    {
        $path = $step->module->learningPath;

        // Must be enrolled to access (unless creator or admin)
        if ($user->isEnrolledIn($path)) {
            return true;
        }

        if ($path->creator_id === $user->id) {
            return true;
        }

        return $user->isAdmin();
    }

    /**
     * Determine whether the user can reorder steps.
     */
    public function reorder(User $user, LearningStep $step): bool
    {
        return $this->update($user, $step);
    }

    /**
     * Determine whether the user can restore the step.
     */
    public function restore(User $user, LearningStep $step): bool
    {
        $path = $step->module->learningPath;

        return $user->isAdmin() || $path->creator_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the step.
     */
    public function forceDelete(User $user, LearningStep $step): bool
    {
        return $user->isAdmin();
    }
}
