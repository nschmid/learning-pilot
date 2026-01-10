<?php

namespace App\Policies;

use App\Models\Module;
use App\Models\User;

class ModulePolicy
{
    /**
     * Determine whether the user can view any modules.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the module.
     */
    public function view(User $user, Module $module): bool
    {
        // Anyone can view modules of published paths
        if ($module->learningPath->is_published) {
            return true;
        }

        // Creator can view their own modules
        if ($module->learningPath->creator_id === $user->id) {
            return true;
        }

        // Admins can view all modules
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create modules.
     */
    public function create(User $user): bool
    {
        return $user->isInstructor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the module.
     */
    public function update(User $user, Module $module): bool
    {
        // Creator can update
        if ($module->learningPath->creator_id === $user->id) {
            return true;
        }

        // Admins can update
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the module.
     */
    public function delete(User $user, Module $module): bool
    {
        // Creator can delete
        if ($module->learningPath->creator_id === $user->id) {
            return true;
        }

        // Admins can delete
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can reorder modules.
     */
    public function reorder(User $user, Module $module): bool
    {
        return $this->update($user, $module);
    }

    /**
     * Determine whether the user can restore the module.
     */
    public function restore(User $user, Module $module): bool
    {
        return $user->isAdmin() || $module->learningPath->creator_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the module.
     */
    public function forceDelete(User $user, Module $module): bool
    {
        return $user->isAdmin();
    }
}
