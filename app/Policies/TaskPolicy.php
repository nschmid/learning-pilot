<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view any tasks.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the task.
     */
    public function view(User $user, Task $task): bool
    {
        $path = $task->step->module->learningPath;

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
     * Determine whether the user can create tasks.
     */
    public function create(User $user): bool
    {
        return $user->isInstructor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the task.
     */
    public function update(User $user, Task $task): bool
    {
        $path = $task->step->module->learningPath;

        // Creator can update
        if ($path->creator_id === $user->id) {
            return true;
        }

        // Admins can update
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the task.
     */
    public function delete(User $user, Task $task): bool
    {
        $path = $task->step->module->learningPath;

        // Creator can delete
        if ($path->creator_id === $user->id) {
            return true;
        }

        // Admins can delete
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can submit to the task.
     */
    public function submit(User $user, Task $task): bool
    {
        $path = $task->step->module->learningPath;

        // Must be enrolled
        return $user->isEnrolledIn($path);
    }

    /**
     * Determine whether the user can review submissions.
     */
    public function reviewSubmissions(User $user, Task $task): bool
    {
        $path = $task->step->module->learningPath;

        // Creator can review
        if ($path->creator_id === $user->id) {
            return true;
        }

        // Admins can review
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the task.
     */
    public function restore(User $user, Task $task): bool
    {
        $path = $task->step->module->learningPath;

        return $user->isAdmin() || $path->creator_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the task.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return $user->isAdmin();
    }
}
