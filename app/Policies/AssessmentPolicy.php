<?php

namespace App\Policies;

use App\Models\Assessment;
use App\Models\User;

class AssessmentPolicy
{
    /**
     * Determine whether the user can view any assessments.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the assessment.
     */
    public function view(User $user, Assessment $assessment): bool
    {
        $path = $assessment->step->module->learningPath;

        // Creator can view
        if ($path->creator_id === $user->id) {
            return true;
        }

        // Enrolled users can view
        if ($user->isEnrolledIn($path)) {
            return true;
        }

        // Admins can view
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create assessments.
     */
    public function create(User $user): bool
    {
        return $user->isInstructor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the assessment.
     */
    public function update(User $user, Assessment $assessment): bool
    {
        $path = $assessment->step->module->learningPath;

        // Creator can update
        if ($path->creator_id === $user->id) {
            return true;
        }

        // Admins can update
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the assessment.
     */
    public function delete(User $user, Assessment $assessment): bool
    {
        $path = $assessment->step->module->learningPath;

        // Creator can delete
        if ($path->creator_id === $user->id) {
            return true;
        }

        // Admins can delete
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can take the assessment.
     */
    public function take(User $user, Assessment $assessment): bool
    {
        $path = $assessment->step->module->learningPath;

        // Must be enrolled
        if (! $user->isEnrolledIn($path)) {
            return false;
        }

        // Check max attempts
        if ($assessment->max_attempts) {
            $attemptCount = $assessment->attempts()
                ->whereHas('enrollment', fn ($q) => $q->where('user_id', $user->id))
                ->count();

            if ($attemptCount >= $assessment->max_attempts) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine whether the user can view results.
     */
    public function viewResults(User $user, Assessment $assessment): bool
    {
        $path = $assessment->step->module->learningPath;

        // Creator can view all results
        if ($path->creator_id === $user->id) {
            return true;
        }

        // Admins can view all results
        if ($user->isAdmin()) {
            return true;
        }

        // Learners can view their own results
        return $user->isEnrolledIn($path);
    }

    /**
     * Determine whether the user can restore the assessment.
     */
    public function restore(User $user, Assessment $assessment): bool
    {
        $path = $assessment->step->module->learningPath;

        return $user->isAdmin() || $path->creator_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the assessment.
     */
    public function forceDelete(User $user, Assessment $assessment): bool
    {
        return $user->isAdmin();
    }
}
