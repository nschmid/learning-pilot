<?php

namespace App\Policies;

use App\Models\AssessmentAttempt;
use App\Models\User;

class AssessmentAttemptPolicy
{
    /**
     * Determine whether the user can view any assessment attempts.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the assessment attempt.
     */
    public function view(User $user, AssessmentAttempt $attempt): bool
    {
        // Owner can view their own attempt
        if ($attempt->enrollment->user_id === $user->id) {
            return true;
        }

        $path = $attempt->assessment->step->module->learningPath;

        // Creator can view all attempts
        if ($path->creator_id === $user->id) {
            return true;
        }

        // Admins can view all attempts
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create assessment attempts.
     */
    public function create(User $user): bool
    {
        return $user->isLearner() || $user->isAdmin();
    }

    /**
     * Determine whether the user can start an attempt (checks enrollment and attempt limits).
     */
    public function start(User $user, AssessmentAttempt $attempt): bool
    {
        $assessment = $attempt->assessment;
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
     * Determine whether the user can submit answers for the attempt.
     */
    public function submit(User $user, AssessmentAttempt $attempt): bool
    {
        // Must be the owner
        if ($attempt->enrollment->user_id !== $user->id) {
            return false;
        }

        // Must be in progress
        if (! $attempt->isInProgress()) {
            return false;
        }

        // Check if time limit exceeded
        $timeRemaining = $attempt->timeRemaining();
        if ($timeRemaining !== null && $timeRemaining <= 0) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can update the assessment attempt.
     */
    public function update(User $user, AssessmentAttempt $attempt): bool
    {
        // Only owner can update (during taking)
        if ($attempt->enrollment->user_id === $user->id && $attempt->isInProgress()) {
            return true;
        }

        // Instructors/admins can update scores
        $path = $attempt->assessment->step->module->learningPath;
        if ($path->creator_id === $user->id) {
            return true;
        }

        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the assessment attempt.
     */
    public function delete(User $user, AssessmentAttempt $attempt): bool
    {
        $path = $attempt->assessment->step->module->learningPath;

        // Creator can delete
        if ($path->creator_id === $user->id) {
            return true;
        }

        // Admins can delete
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the results of an attempt.
     */
    public function viewResults(User $user, AssessmentAttempt $attempt): bool
    {
        // Must be completed
        if (! $attempt->isCompleted()) {
            return false;
        }

        $assessment = $attempt->assessment;

        // Check if results should be shown immediately
        if (! $assessment->show_score_immediately) {
            $path = $assessment->step->module->learningPath;

            // Only instructor/admin can see results if not shown immediately
            if ($path->creator_id !== $user->id && ! $user->isAdmin()) {
                return false;
            }
        }

        // Owner can view their own results
        if ($attempt->enrollment->user_id === $user->id) {
            return true;
        }

        $path = $assessment->step->module->learningPath;

        // Creator can view all results
        if ($path->creator_id === $user->id) {
            return true;
        }

        // Admins can view all results
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view correct answers.
     */
    public function viewCorrectAnswers(User $user, AssessmentAttempt $attempt): bool
    {
        // Must be completed
        if (! $attempt->isCompleted()) {
            return false;
        }

        $assessment = $attempt->assessment;

        // Check if correct answers should be shown
        if ($assessment->show_correct_answers && $attempt->enrollment->user_id === $user->id) {
            return true;
        }

        $path = $assessment->step->module->learningPath;

        // Creator can always see correct answers
        if ($path->creator_id === $user->id) {
            return true;
        }

        // Admins can always see correct answers
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the assessment attempt.
     */
    public function restore(User $user, AssessmentAttempt $attempt): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the assessment attempt.
     */
    public function forceDelete(User $user, AssessmentAttempt $attempt): bool
    {
        return $user->isAdmin();
    }
}
