<?php

namespace App\Repositories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends BaseRepository
{
    protected function model(): string
    {
        return User::class;
    }

    /**
     * Find user by email.
     */
    public function findByEmail(string $email): ?User
    {
        return $this->findFirstBy('email', $email);
    }

    /**
     * Get users by role.
     */
    public function getByRole(UserRole $role): Collection
    {
        return $this->query
            ->where('role', $role)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get active users.
     */
    public function getActive(): self
    {
        $this->query = $this->query->where('is_active', true);

        return $this;
    }

    /**
     * Get inactive users.
     */
    public function getInactive(): Collection
    {
        return $this->query
            ->where('is_active', false)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get learners.
     */
    public function getLearners(): Collection
    {
        return $this->getByRole(UserRole::Learner);
    }

    /**
     * Get instructors.
     */
    public function getInstructors(): Collection
    {
        return $this->getByRole(UserRole::Instructor);
    }

    /**
     * Get admins.
     */
    public function getAdmins(): Collection
    {
        return $this->getByRole(UserRole::Admin);
    }

    /**
     * Search users by name or email.
     */
    public function search(string $term): self
    {
        $this->query = $this->query->where(function ($query) use ($term) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%");
        });

        return $this;
    }

    /**
     * Get users for a team.
     */
    public function getByTeam(string $teamId): Collection
    {
        return $this->query
            ->whereHas('teams', fn ($q) => $q->where('teams.id', $teamId))
            ->orderBy('name')
            ->get();
    }

    /**
     * Get user with enrollments.
     */
    public function getWithEnrollments(string $userId): ?User
    {
        return $this->query
            ->with(['enrollments.learningPath'])
            ->find($userId);
    }

    /**
     * Get user with full learning data.
     */
    public function getWithLearningData(string $userId): ?User
    {
        return $this->query
            ->with([
                'enrollments.learningPath',
                'enrollments.stepProgress',
                'enrollments.certificate',
                'bookmarks.step',
                'notes',
            ])
            ->find($userId);
    }

    /**
     * Get user statistics.
     */
    public function getStats(string $userId): array
    {
        $user = $this->getWithLearningData($userId);

        if (! $user) {
            return [];
        }

        $enrollments = $user->enrollments;

        return [
            'total_enrollments' => $enrollments->count(),
            'completed_paths' => $enrollments->where('status', 'completed')->count(),
            'active_paths' => $enrollments->where('status', 'active')->count(),
            'total_points' => $enrollments->sum('points_earned'),
            'total_time_spent' => $enrollments->sum('total_time_spent_seconds'),
            'certificates_earned' => $enrollments->whereNotNull('certificate')->count(),
            'average_progress' => $enrollments->avg('progress_percent') ?? 0,
        ];
    }

    /**
     * Get recently active users.
     */
    public function getRecentlyActive(int $days = 7): Collection
    {
        return $this->query
            ->whereHas('enrollments', fn ($q) => $q->where('last_activity_at', '>=', now()->subDays($days)))
            ->with(['enrollments' => fn ($q) => $q->orderBy('last_activity_at', 'desc')->limit(1)])
            ->get();
    }

    /**
     * Get top learners by points.
     */
    public function getTopLearners(int $limit = 10): Collection
    {
        return $this->query
            ->where('role', UserRole::Learner)
            ->whereHas('enrollments')
            ->withSum('enrollments', 'points_earned')
            ->orderBy('enrollments_sum_points_earned', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Activate user.
     */
    public function activate(string $userId): bool
    {
        return $this->update($userId, ['is_active' => true]);
    }

    /**
     * Deactivate user.
     */
    public function deactivate(string $userId): bool
    {
        return $this->update($userId, ['is_active' => false]);
    }

    /**
     * Update user role.
     */
    public function updateRole(string $userId, UserRole $role): bool
    {
        return $this->update($userId, ['role' => $role]);
    }
}
