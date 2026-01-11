<?php

namespace App\Policies;

use App\Models\AiFeedbackReport;
use App\Models\User;

class AiFeedbackReportPolicy
{
    /**
     * Determine whether the user can view any feedback reports.
     */
    public function viewAny(User $user): bool
    {
        // Only admins can view all feedback reports
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the feedback report.
     */
    public function view(User $user, AiFeedbackReport $report): bool
    {
        // Users can view their own feedback
        if ($report->user_id === $user->id) {
            return true;
        }

        // Admins can view all feedback
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create feedback reports.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the feedback report.
     */
    public function update(User $user, AiFeedbackReport $report): bool
    {
        // Users can update their own pending feedback
        if ($report->user_id === $user->id && $report->status === 'pending') {
            return true;
        }

        // Admins can update (for moderation status)
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can moderate the feedback.
     */
    public function moderate(User $user, AiFeedbackReport $report): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the feedback report.
     */
    public function delete(User $user, AiFeedbackReport $report): bool
    {
        // Users can delete their own pending feedback
        if ($report->user_id === $user->id && $report->status === 'pending') {
            return true;
        }

        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the feedback report.
     */
    public function restore(User $user, AiFeedbackReport $report): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the feedback report.
     */
    public function forceDelete(User $user, AiFeedbackReport $report): bool
    {
        return $user->isAdmin();
    }
}
