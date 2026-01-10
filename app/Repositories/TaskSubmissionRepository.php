<?php

namespace App\Repositories;

use App\Enums\SubmissionStatus;
use App\Models\Enrollment;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class TaskSubmissionRepository extends BaseRepository
{
    protected function model(): string
    {
        return TaskSubmission::class;
    }

    /**
     * Get submission for a task and enrollment.
     */
    public function getByTaskAndEnrollment(Task $task, Enrollment $enrollment): ?TaskSubmission
    {
        return $this->query
            ->where('task_id', $task->id)
            ->where('enrollment_id', $enrollment->id)
            ->first();
    }

    /**
     * Get submissions by status.
     */
    public function getByStatus(SubmissionStatus $status): self
    {
        $this->query = $this->query->where('status', $status);

        return $this;
    }

    /**
     * Get pending submissions.
     */
    public function getPending(): Collection
    {
        return $this->query
            ->where('status', SubmissionStatus::Pending)
            ->with(['task.step.module.learningPath', 'enrollment.user'])
            ->orderBy('submitted_at', 'asc')
            ->get();
    }

    /**
     * Get submissions for review by instructor.
     */
    public function getForReview(User $instructor): Collection
    {
        return $this->query
            ->where('status', SubmissionStatus::Pending)
            ->whereHas('task.step.module.learningPath', fn ($q) => $q->where('creator_id', $instructor->id))
            ->with(['task.step.module.learningPath', 'enrollment.user'])
            ->orderBy('submitted_at', 'asc')
            ->get();
    }

    /**
     * Get submissions by enrollment.
     */
    public function getByEnrollment(Enrollment $enrollment): Collection
    {
        return $this->query
            ->where('enrollment_id', $enrollment->id)
            ->with(['task'])
            ->orderBy('submitted_at', 'desc')
            ->get();
    }

    /**
     * Get submissions by task.
     */
    public function getByTask(Task $task): Collection
    {
        return $this->query
            ->where('task_id', $task->id)
            ->with(['enrollment.user'])
            ->orderBy('submitted_at', 'desc')
            ->get();
    }

    /**
     * Submit a task.
     */
    public function submit(Task $task, Enrollment $enrollment, array $data): TaskSubmission
    {
        return $this->updateOrCreate(
            [
                'task_id' => $task->id,
                'enrollment_id' => $enrollment->id,
            ],
            [
                'content' => $data['content'] ?? null,
                'file_paths' => $data['file_paths'] ?? null,
                'status' => SubmissionStatus::Pending,
                'submitted_at' => now(),
            ]
        );
    }

    /**
     * Grade a submission.
     */
    public function grade(string $submissionId, User $reviewer, int $score, ?string $feedback = null): bool
    {
        return $this->update($submissionId, [
            'status' => SubmissionStatus::Graded,
            'score' => $score,
            'feedback' => $feedback,
            'reviewer_id' => $reviewer->id,
            'reviewed_at' => now(),
        ]);
    }

    /**
     * Request revision.
     */
    public function requestRevision(string $submissionId, User $reviewer, string $feedback): bool
    {
        return $this->update($submissionId, [
            'status' => SubmissionStatus::RevisionRequested,
            'feedback' => $feedback,
            'reviewer_id' => $reviewer->id,
            'reviewed_at' => now(),
        ]);
    }

    /**
     * Get submission statistics for a task.
     */
    public function getTaskStats(Task $task): array
    {
        $submissions = $this->getByTask($task);

        return [
            'total' => $submissions->count(),
            'pending' => $submissions->where('status', SubmissionStatus::Pending)->count(),
            'graded' => $submissions->where('status', SubmissionStatus::Graded)->count(),
            'revision_requested' => $submissions->where('status', SubmissionStatus::RevisionRequested)->count(),
            'average_score' => $submissions->whereNotNull('score')->avg('score') ?? 0,
            'highest_score' => $submissions->whereNotNull('score')->max('score') ?? 0,
        ];
    }

    /**
     * Get recent submissions.
     */
    public function getRecent(int $limit = 10): Collection
    {
        return $this->query
            ->with(['task.step.module.learningPath', 'enrollment.user'])
            ->orderBy('submitted_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
