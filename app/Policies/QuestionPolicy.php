<?php

namespace App\Policies;

use App\Models\Question;
use App\Models\User;

class QuestionPolicy
{
    /**
     * Determine whether the user can view any questions.
     */
    public function viewAny(User $user): bool
    {
        return $user->isInstructor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can view the question.
     */
    public function view(User $user, Question $question): bool
    {
        $path = $question->assessment->step->module->learningPath;

        // Creator can view
        if ($path->creator_id === $user->id) {
            return true;
        }

        // Enrolled users can view during assessment
        if ($user->isEnrolledIn($path)) {
            return true;
        }

        // Admins can view
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create questions.
     */
    public function create(User $user): bool
    {
        return $user->isInstructor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the question.
     */
    public function update(User $user, Question $question): bool
    {
        $path = $question->assessment->step->module->learningPath;

        // Creator can update
        if ($path->creator_id === $user->id) {
            return true;
        }

        // Admins can update
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the question.
     */
    public function delete(User $user, Question $question): bool
    {
        $path = $question->assessment->step->module->learningPath;

        // Creator can delete
        if ($path->creator_id === $user->id) {
            return true;
        }

        // Admins can delete
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can duplicate the question.
     */
    public function duplicate(User $user, Question $question): bool
    {
        $path = $question->assessment->step->module->learningPath;

        // Creator can duplicate
        if ($path->creator_id === $user->id) {
            return true;
        }

        // Admins can duplicate
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can reorder questions.
     */
    public function reorder(User $user, Question $question): bool
    {
        $path = $question->assessment->step->module->learningPath;

        // Creator can reorder
        if ($path->creator_id === $user->id) {
            return true;
        }

        // Admins can reorder
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the question.
     */
    public function restore(User $user, Question $question): bool
    {
        $path = $question->assessment->step->module->learningPath;

        return $user->isAdmin() || $path->creator_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the question.
     */
    public function forceDelete(User $user, Question $question): bool
    {
        return $user->isAdmin();
    }
}
