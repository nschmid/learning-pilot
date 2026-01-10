<?php

namespace App\Repositories;

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\LearningPath;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class EnrollmentRepository extends BaseRepository
{
    protected function model(): string
    {
        return Enrollment::class;
    }

    /**
     * Get enrollment by user and path.
     */
    public function findByUserAndPath(User $user, LearningPath $path): ?Enrollment
    {
        return $this->query
            ->where('user_id', $user->id)
            ->where('learning_path_id', $path->id)
            ->first();
    }

    /**
     * Get enrollments for a user.
     */
    public function getByUser(User $user): Collection
    {
        return $this->query
            ->where('user_id', $user->id)
            ->with(['learningPath.creator', 'learningPath.category'])
            ->orderBy('last_activity_at', 'desc')
            ->get();
    }

    /**
     * Get active enrollments for a user.
     */
    public function getActiveByUser(User $user): Collection
    {
        return $this->query
            ->where('user_id', $user->id)
            ->where('status', EnrollmentStatus::Active)
            ->with(['learningPath'])
            ->orderBy('last_activity_at', 'desc')
            ->get();
    }

    /**
     * Get completed enrollments for a user.
     */
    public function getCompletedByUser(User $user): Collection
    {
        return $this->query
            ->where('user_id', $user->id)
            ->where('status', EnrollmentStatus::Completed)
            ->with(['learningPath', 'certificate'])
            ->orderBy('completed_at', 'desc')
            ->get();
    }

    /**
     * Get enrollments for a learning path.
     */
    public function getByPath(LearningPath $path): Collection
    {
        return $this->query
            ->where('learning_path_id', $path->id)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get enrollments by status.
     */
    public function getByStatus(EnrollmentStatus $status): self
    {
        $this->query = $this->query->where('status', $status);

        return $this;
    }

    /**
     * Check if user is enrolled in a path.
     */
    public function isEnrolled(User $user, LearningPath $path): bool
    {
        return $this->query
            ->where('user_id', $user->id)
            ->where('learning_path_id', $path->id)
            ->exists();
    }

    /**
     * Get enrollment with progress details.
     */
    public function getWithProgress(string $enrollmentId): ?Enrollment
    {
        return $this->query
            ->with([
                'learningPath.modules.steps',
                'stepProgress',
                'assessmentAttempts',
                'taskSubmissions',
            ])
            ->find($enrollmentId);
    }

    /**
     * Update last activity timestamp.
     */
    public function updateLastActivity(string $enrollmentId): bool
    {
        return $this->update($enrollmentId, [
            'last_activity_at' => now(),
        ]);
    }

    /**
     * Update progress percentage.
     */
    public function updateProgress(string $enrollmentId, float $progressPercent): bool
    {
        return $this->update($enrollmentId, [
            'progress_percent' => $progressPercent,
        ]);
    }

    /**
     * Mark enrollment as completed.
     */
    public function markCompleted(string $enrollmentId): bool
    {
        return $this->update($enrollmentId, [
            'status' => EnrollmentStatus::Completed,
            'completed_at' => now(),
            'progress_percent' => 100,
        ]);
    }

    /**
     * Get enrollment statistics for a path.
     */
    public function getPathStats(LearningPath $path): array
    {
        $enrollments = $this->getByPath($path);

        return [
            'total' => $enrollments->count(),
            'active' => $enrollments->where('status', EnrollmentStatus::Active)->count(),
            'completed' => $enrollments->where('status', EnrollmentStatus::Completed)->count(),
            'paused' => $enrollments->where('status', EnrollmentStatus::Paused)->count(),
            'average_progress' => $enrollments->avg('progress_percent') ?? 0,
            'average_time_spent' => $enrollments->avg('total_time_spent_seconds') ?? 0,
            'completion_rate' => $enrollments->count() > 0
                ? ($enrollments->where('status', EnrollmentStatus::Completed)->count() / $enrollments->count()) * 100
                : 0,
        ];
    }

    /**
     * Get recently active enrollments.
     */
    public function getRecentlyActive(int $days = 7): Collection
    {
        return $this->query
            ->where('last_activity_at', '>=', now()->subDays($days))
            ->with(['user', 'learningPath'])
            ->orderBy('last_activity_at', 'desc')
            ->get();
    }

    /**
     * Get enrollments for instructor's paths.
     */
    public function getForInstructor(User $instructor): Collection
    {
        return $this->query
            ->whereHas('learningPath', fn ($q) => $q->where('creator_id', $instructor->id))
            ->with(['user', 'learningPath'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Enroll a user in a path.
     */
    public function enroll(User $user, LearningPath $path): Enrollment
    {
        return $this->create([
            'user_id' => $user->id,
            'learning_path_id' => $path->id,
            'status' => EnrollmentStatus::Active,
            'progress_percent' => 0,
            'started_at' => now(),
            'last_activity_at' => now(),
        ]);
    }
}
