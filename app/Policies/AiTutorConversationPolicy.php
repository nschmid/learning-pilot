<?php

namespace App\Policies;

use App\Models\AiTutorConversation;
use App\Models\User;

class AiTutorConversationPolicy
{
    /**
     * Determine whether the user can view any conversations.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the conversation.
     */
    public function view(User $user, AiTutorConversation $conversation): bool
    {
        // Users can only view their own conversations
        if ($conversation->user_id === $user->id) {
            return true;
        }

        // Admins can view for support/moderation
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create conversations.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can send messages in the conversation.
     */
    public function sendMessage(User $user, AiTutorConversation $conversation): bool
    {
        // Only the owner can send messages
        if ($conversation->user_id !== $user->id) {
            return false;
        }

        // Conversation must be active
        return $conversation->status === 'active';
    }

    /**
     * Determine whether the user can update the conversation.
     */
    public function update(User $user, AiTutorConversation $conversation): bool
    {
        // Users can update their own conversations (e.g., rename, archive)
        if ($conversation->user_id === $user->id) {
            return true;
        }

        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the conversation.
     */
    public function delete(User $user, AiTutorConversation $conversation): bool
    {
        // Users can delete their own conversations
        if ($conversation->user_id === $user->id) {
            return true;
        }

        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the conversation.
     */
    public function restore(User $user, AiTutorConversation $conversation): bool
    {
        return $conversation->user_id === $user->id || $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the conversation.
     */
    public function forceDelete(User $user, AiTutorConversation $conversation): bool
    {
        return $user->isAdmin();
    }
}
