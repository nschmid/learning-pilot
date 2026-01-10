<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\LearningPath;
use App\Models\User;

class LearningPathPolicy
{
    /**
     * Determine whether the user can view any learning paths.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the learning path.
     */
    public function view(User $user, LearningPath $learningPath): bool
    {
        // Published paths are viewable by all
        if ($learningPath->is_published) {
            return true;
        }

        // Admin can view all
        if ($user->role === UserRole::Admin) {
            return true;
        }

        // Creator can view their own
        if ($user->id === $learningPath->creator_id) {
            return true;
        }

        // Team members can view team paths
        if ($learningPath->team_id && $user->belongsToTeam($learningPath->team)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create learning paths.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, [UserRole::Admin, UserRole::Instructor], true);
    }

    /**
     * Determine whether the user can update the learning path.
     */
    public function update(User $user, LearningPath $learningPath): bool
    {
        // Admin can update all
        if ($user->role === UserRole::Admin) {
            return true;
        }

        // Creator can update their own
        if ($user->role === UserRole::Instructor && $user->id === $learningPath->creator_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the learning path.
     */
    public function delete(User $user, LearningPath $learningPath): bool
    {
        // Admin can delete all
        if ($user->role === UserRole::Admin) {
            return true;
        }

        // Creator can delete their own (if not published)
        if ($user->id === $learningPath->creator_id && ! $learningPath->is_published) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the learning path.
     */
    public function restore(User $user, LearningPath $learningPath): bool
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can permanently delete the learning path.
     */
    public function forceDelete(User $user, LearningPath $learningPath): bool
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can publish the learning path.
     */
    public function publish(User $user, LearningPath $learningPath): bool
    {
        return $this->update($user, $learningPath);
    }

    /**
     * Determine whether the user can enroll in the learning path.
     */
    public function enroll(User $user, LearningPath $learningPath): bool
    {
        // Must be published
        if (! $learningPath->is_published) {
            return false;
        }

        // Can't enroll in your own path
        if ($user->id === $learningPath->creator_id) {
            return false;
        }

        // Check if not already enrolled
        return ! $user->enrollments()
            ->where('learning_path_id', $learningPath->id)
            ->exists();
    }
}
