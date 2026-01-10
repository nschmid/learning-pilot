<?php

namespace App\Repositories;

use App\Enums\AssessmentType;
use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class AssessmentRepository extends BaseRepository
{
    protected function model(): string
    {
        return Assessment::class;
    }

    /**
     * Get assessment with questions.
     */
    public function getWithQuestions(string $assessmentId): ?Assessment
    {
        return $this->query
            ->with(['questions' => fn ($q) => $q->ordered()->with('options')])
            ->find($assessmentId);
    }

    /**
     * Get assessments by type.
     */
    public function getByType(AssessmentType $type): self
    {
        $this->query = $this->query->where('assessment_type', $type);

        return $this;
    }

    /**
     * Get assessments for a step.
     */
    public function getByStep(string $stepId): Collection
    {
        return $this->query
            ->where('step_id', $stepId)
            ->with('questions')
            ->get();
    }

    /**
     * Get first assessment for a step.
     */
    public function getFirstByStep(string $stepId): ?Assessment
    {
        return $this->query
            ->where('step_id', $stepId)
            ->first();
    }

    /**
     * Get user's attempts for an assessment.
     */
    public function getUserAttempts(Assessment $assessment, Enrollment $enrollment): Collection
    {
        return $assessment->attempts()
            ->where('enrollment_id', $enrollment->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get user's best attempt for an assessment.
     */
    public function getUserBestAttempt(Assessment $assessment, Enrollment $enrollment): ?AssessmentAttempt
    {
        return $assessment->attempts()
            ->where('enrollment_id', $enrollment->id)
            ->whereNotNull('completed_at')
            ->orderBy('score_percent', 'desc')
            ->first();
    }

    /**
     * Check if user can attempt assessment.
     */
    public function canAttempt(Assessment $assessment, Enrollment $enrollment): bool
    {
        if (! $assessment->max_attempts) {
            return true;
        }

        $attemptCount = $assessment->attempts()
            ->where('enrollment_id', $enrollment->id)
            ->count();

        return $attemptCount < $assessment->max_attempts;
    }

    /**
     * Get remaining attempts count.
     */
    public function getRemainingAttempts(Assessment $assessment, Enrollment $enrollment): ?int
    {
        if (! $assessment->max_attempts) {
            return null; // Unlimited
        }

        $attemptCount = $assessment->attempts()
            ->where('enrollment_id', $enrollment->id)
            ->count();

        return max(0, $assessment->max_attempts - $attemptCount);
    }

    /**
     * Create a new attempt.
     */
    public function createAttempt(Assessment $assessment, Enrollment $enrollment): AssessmentAttempt
    {
        $attemptNumber = $assessment->attempts()
            ->where('enrollment_id', $enrollment->id)
            ->count() + 1;

        return AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'enrollment_id' => $enrollment->id,
            'attempt_number' => $attemptNumber,
            'started_at' => now(),
        ]);
    }

    /**
     * Get assessment statistics.
     */
    public function getStats(string $assessmentId): array
    {
        $assessment = $this->with(['attempts'])->find($assessmentId);

        if (! $assessment) {
            return [];
        }

        $completedAttempts = $assessment->attempts->whereNotNull('completed_at');

        return [
            'total_attempts' => $assessment->attempts->count(),
            'completed_attempts' => $completedAttempts->count(),
            'passed_attempts' => $completedAttempts->where('passed', true)->count(),
            'average_score' => $completedAttempts->avg('score_percent') ?? 0,
            'highest_score' => $completedAttempts->max('score_percent') ?? 0,
            'lowest_score' => $completedAttempts->min('score_percent') ?? 0,
            'average_time_spent' => $completedAttempts->avg('time_spent_seconds') ?? 0,
            'pass_rate' => $completedAttempts->count() > 0
                ? ($completedAttempts->where('passed', true)->count() / $completedAttempts->count()) * 100
                : 0,
        ];
    }

    /**
     * Get question statistics for an assessment.
     */
    public function getQuestionStats(string $assessmentId): Collection
    {
        $assessment = $this->getWithQuestions($assessmentId);

        if (! $assessment) {
            return collect();
        }

        return $assessment->questions->map(function ($question) {
            $responses = $question->responses;
            $correctResponses = $responses->where('is_correct', true);

            return [
                'question_id' => $question->id,
                'question_text' => $question->question_text,
                'total_responses' => $responses->count(),
                'correct_responses' => $correctResponses->count(),
                'accuracy_rate' => $responses->count() > 0
                    ? ($correctResponses->count() / $responses->count()) * 100
                    : 0,
            ];
        });
    }

    /**
     * Get assessments requiring review (text answers).
     */
    public function getRequiringReview(User $instructor): Collection
    {
        return $this->query
            ->whereHas('step.module.learningPath', fn ($q) => $q->where('creator_id', $instructor->id))
            ->whereHas('attempts.responses', fn ($q) => $q->whereNull('is_correct'))
            ->with(['step.module.learningPath', 'attempts.responses' => fn ($q) => $q->whereNull('is_correct')])
            ->get();
    }
}
