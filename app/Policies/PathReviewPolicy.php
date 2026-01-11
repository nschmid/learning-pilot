<?php

namespace App\Policies;

use App\Models\PathReview;
use App\Models\User;

class PathReviewPolicy
{
    /**
     * Determine whether the user can view any reviews.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the review.
     */
    public function view(User $user, PathReview $review): bool
    {
        // Published reviews are visible to everyone
        if ($review->is_published ?? true) {
            return true;
        }

        // Authors can see their own unpublished reviews
        if ($review->user_id === $user->id) {
            return true;
        }

        // Path creators and admins can see all reviews
        if ($review->learningPath->creator_id === $user->id) {
            return true;
        }

        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create reviews.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the review.
     */
    public function update(User $user, PathReview $review): bool
    {
        // Authors can update their own reviews
        if ($review->user_id === $user->id) {
            return true;
        }

        // Admins can moderate
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the review.
     */
    public function delete(User $user, PathReview $review): bool
    {
        // Authors can delete their own reviews
        if ($review->user_id === $user->id) {
            return true;
        }

        // Admins can moderate
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can moderate reviews.
     */
    public function moderate(User $user, PathReview $review): bool
    {
        // Path creators can moderate reviews on their paths
        if ($review->learningPath->creator_id === $user->id) {
            return true;
        }

        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the review.
     */
    public function restore(User $user, PathReview $review): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the review.
     */
    public function forceDelete(User $user, PathReview $review): bool
    {
        return $user->isAdmin();
    }
}
