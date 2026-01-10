<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Enrollment;
use App\Models\User;

class EnrollmentPolicy
{
    /**
     * Determine whether the user can view any enrollments.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [UserRole::Admin, UserRole::Instructor], true);
    }

    /**
     * Determine whether the user can view the enrollment.
     */
    public function view(User $user, Enrollment $enrollment): bool
    {
        // Admin can view all
        if ($user->role === UserRole::Admin) {
            return true;
        }

        // User can view their own enrollment
        if ($user->id === $enrollment->user_id) {
            return true;
        }

        // Instructor can view enrollments in their paths
        if ($user->role === UserRole::Instructor) {
            return $enrollment->learningPath->creator_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create enrollments.
     */
    public function create(User $user): bool
    {
        return true; // Users can enroll themselves
    }

    /**
     * Determine whether the user can update the enrollment.
     */
    public function update(User $user, Enrollment $enrollment): bool
    {
        // Admin can update all
        if ($user->role === UserRole::Admin) {
            return true;
        }

        // Instructor can update enrollments in their paths
        if ($user->role === UserRole::Instructor) {
            return $enrollment->learningPath->creator_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the enrollment.
     */
    public function delete(User $user, Enrollment $enrollment): bool
    {
        // Admin can delete all
        if ($user->role === UserRole::Admin) {
            return true;
        }

        // User can cancel their own enrollment
        if ($user->id === $enrollment->user_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the enrollment progress.
     */
    public function viewProgress(User $user, Enrollment $enrollment): bool
    {
        return $this->view($user, $enrollment);
    }
}
