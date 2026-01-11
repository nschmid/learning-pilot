<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserNote;

class UserNotePolicy
{
    /**
     * Determine whether the user can view any notes.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the note.
     */
    public function view(User $user, UserNote $note): bool
    {
        // Users can only view their own notes
        if ($note->user_id === $user->id) {
            return true;
        }

        // Admins can view for support purposes
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create notes.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the note.
     */
    public function update(User $user, UserNote $note): bool
    {
        // Users can only update their own notes
        return $note->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the note.
     */
    public function delete(User $user, UserNote $note): bool
    {
        // Users can delete their own notes
        if ($note->user_id === $user->id) {
            return true;
        }

        // Admins can delete
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the note.
     */
    public function restore(User $user, UserNote $note): bool
    {
        return $note->user_id === $user->id || $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the note.
     */
    public function forceDelete(User $user, UserNote $note): bool
    {
        return $user->isAdmin();
    }
}
