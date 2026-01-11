<?php

namespace App\Policies;

use App\Models\AiPracticeSession;
use App\Models\User;

class AiPracticeSessionPolicy
{
    /**
     * Determine whether the user can view any practice sessions.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the practice session.
     */
    public function view(User $user, AiPracticeSession $session): bool
    {
        // Users can only view their own sessions
        if ($session->user_id === $user->id) {
            return true;
        }

        // Admins can view for analytics
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create practice sessions.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can answer questions in the session.
     */
    public function answer(User $user, AiPracticeSession $session): bool
    {
        // Only the owner can answer
        if ($session->user_id !== $user->id) {
            return false;
        }

        // Session must not be completed
        return $session->status !== 'completed';
    }

    /**
     * Determine whether the user can update the practice session.
     */
    public function update(User $user, AiPracticeSession $session): bool
    {
        // Users can update their own sessions
        if ($session->user_id === $user->id) {
            return true;
        }

        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the practice session.
     */
    public function delete(User $user, AiPracticeSession $session): bool
    {
        // Users can delete their own sessions
        if ($session->user_id === $user->id) {
            return true;
        }

        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the practice session.
     */
    public function restore(User $user, AiPracticeSession $session): bool
    {
        return $session->user_id === $user->id || $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the practice session.
     */
    public function forceDelete(User $user, AiPracticeSession $session): bool
    {
        return $user->isAdmin();
    }
}
