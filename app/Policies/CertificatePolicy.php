<?php

namespace App\Policies;

use App\Models\Certificate;
use App\Models\User;

class CertificatePolicy
{
    /**
     * Determine whether the user can view any certificates.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the certificate.
     */
    public function view(User $user, Certificate $certificate): bool
    {
        // Owner can view their certificate
        if ($certificate->enrollment->user_id === $user->id) {
            return true;
        }

        // Path creator can view certificates
        if ($certificate->enrollment->learningPath->creator_id === $user->id) {
            return true;
        }

        // Admins can view all certificates
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create certificates.
     */
    public function create(User $user): bool
    {
        return $user->isInstructor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the certificate.
     */
    public function update(User $user, Certificate $certificate): bool
    {
        // Only admins can update certificates
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the certificate.
     */
    public function delete(User $user, Certificate $certificate): bool
    {
        // Only admins can delete certificates
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can download the certificate.
     */
    public function download(User $user, Certificate $certificate): bool
    {
        // Owner can download
        if ($certificate->enrollment->user_id === $user->id) {
            return true;
        }

        // Path creator can download
        if ($certificate->enrollment->learningPath->creator_id === $user->id) {
            return true;
        }

        // Admins can download
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can verify certificates.
     */
    public function verify(User $user): bool
    {
        // Anyone can verify certificates (public verification)
        return true;
    }

    /**
     * Determine whether the user can revoke the certificate.
     */
    public function revoke(User $user, Certificate $certificate): bool
    {
        // Only admins can revoke certificates
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the certificate.
     */
    public function restore(User $user, Certificate $certificate): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the certificate.
     */
    public function forceDelete(User $user, Certificate $certificate): bool
    {
        return $user->isAdmin();
    }
}
