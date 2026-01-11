<?php

namespace App\Policies;

use App\Models\LearningMaterial;
use App\Models\User;

class LearningMaterialPolicy
{
    /**
     * Determine whether the user can view any learning materials.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the learning material.
     */
    public function view(User $user, LearningMaterial $material): bool
    {
        $path = $material->step->module->learningPath;

        // Published paths - enrolled users can view
        if ($path->is_published && $user->isEnrolledIn($path)) {
            return true;
        }

        // Creator can view
        if ($path->creator_id === $user->id) {
            return true;
        }

        // Admins can view
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create learning materials.
     */
    public function create(User $user): bool
    {
        return $user->isInstructor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the learning material.
     */
    public function update(User $user, LearningMaterial $material): bool
    {
        $path = $material->step->module->learningPath;

        // Creator can update
        if ($path->creator_id === $user->id) {
            return true;
        }

        // Admins can update
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the learning material.
     */
    public function delete(User $user, LearningMaterial $material): bool
    {
        $path = $material->step->module->learningPath;

        // Creator can delete
        if ($path->creator_id === $user->id) {
            return true;
        }

        // Admins can delete
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the learning material.
     */
    public function restore(User $user, LearningMaterial $material): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the learning material.
     */
    public function forceDelete(User $user, LearningMaterial $material): bool
    {
        return $user->isAdmin();
    }
}
