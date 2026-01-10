<?php

namespace App\Services;

use App\Enums\QuestionType;
use App\Enums\StepProgressStatus;
use App\Models\AssessmentAttempt;
use App\Models\Question;
use App\Models\QuestionResponse;
use App\Models\StepProgress;
use Illuminate\Support\Facades\DB;

class AssessmentGradingService
{
    /**
     * Grade an assessment attempt.
     */
    public function gradeAttempt(AssessmentAttempt $attempt, array $answers): array
    {
        return DB::transaction(function () use ($attempt, $answers) {
            $assessment = $attempt->assessment;
            $questions = $assessment->questions()->with('options')->get();

            $totalPoints = 0;
            $earnedPoints = 0;
            $results = [];

            foreach ($questions as $question) {
                $userAnswer = $answers[$question->id] ?? null;
                $gradeResult = $this->gradeQuestion($question, $userAnswer);

                $totalPoints += $question->points;
                $earnedPoints += $gradeResult['points'];

                // Save question response
                QuestionResponse::updateOrCreate(
                    [
                        'attempt_id' => $attempt->id,
                        'question_id' => $question->id,
                    ],
                    [
                        'user_answer' => $this->serializeAnswer($userAnswer),
                        'is_correct' => $gradeResult['is_correct'],
                        'points_earned' => $gradeResult['points'],
                    ]
                );

                $results[$question->id] = $gradeResult;
            }

            // Calculate score percentage
            $scorePercent = $totalPoints > 0
                ? round(($earnedPoints / $totalPoints) * 100, 2)
                : 0;

            // Determine if passed
            $passed = $scorePercent >= $assessment->passing_score_percent;

            // Calculate time spent
            $timeSpent = now()->diffInSeconds($attempt->started_at);

            // Update attempt
            $attempt->update([
                'completed_at' => now(),
                'score_percent' => $scorePercent,
                'points_earned' => $earnedPoints,
                'passed' => $passed,
                'time_spent_seconds' => $timeSpent,
                'answers' => $answers,
            ]);

            // Update step progress if passed
            if ($passed) {
                $this->updateStepProgress($attempt);
            }

            return [
                'score_percent' => $scorePercent,
                'points_earned' => $earnedPoints,
                'total_points' => $totalPoints,
                'passed' => $passed,
                'results' => $results,
            ];
        });
    }

    /**
     * Grade a single question.
     */
    protected function gradeQuestion(Question $question, mixed $userAnswer): array
    {
        if (! $question->isAutoGradable()) {
            // Text questions need manual grading
            return [
                'is_correct' => null,
                'points' => 0,
                'needs_review' => true,
            ];
        }

        $isCorrect = false;
        $partialCredit = false;

        switch ($question->question_type) {
            case QuestionType::SingleChoice:
                $isCorrect = $this->gradeSingleChoice($question, $userAnswer);
                break;

            case QuestionType::MultipleChoice:
                $result = $this->gradeMultipleChoice($question, $userAnswer);
                $isCorrect = $result['is_correct'];
                $partialCredit = $result['partial'];
                break;

            case QuestionType::TrueFalse:
                $isCorrect = $this->gradeTrueFalse($question, $userAnswer);
                break;

            case QuestionType::Matching:
                $result = $this->gradeMatching($question, $userAnswer);
                $isCorrect = $result['is_correct'];
                $partialCredit = $result['partial'];
                break;
        }

        $points = $isCorrect ? $question->points : ($partialCredit ? $question->points * $partialCredit : 0);

        return [
            'is_correct' => $isCorrect,
            'points' => $points,
            'needs_review' => false,
        ];
    }

    /**
     * Grade a single choice question.
     */
    protected function gradeSingleChoice(Question $question, mixed $userAnswer): bool
    {
        if (empty($userAnswer)) {
            return false;
        }

        $correctOption = $question->correctOptions()->first();

        return $correctOption && $correctOption->id === $userAnswer;
    }

    /**
     * Grade a multiple choice question with partial credit.
     */
    protected function gradeMultipleChoice(Question $question, mixed $userAnswer): array
    {
        $userAnswers = is_array($userAnswer) ? $userAnswer : [];
        $correctOptionIds = $question->correctOptions()->pluck('id')->toArray();
        $allOptionIds = $question->options->pluck('id')->toArray();

        if (empty($userAnswers)) {
            return ['is_correct' => false, 'partial' => 0];
        }

        // Check for exact match
        $selectedCorrect = array_intersect($userAnswers, $correctOptionIds);
        $selectedIncorrect = array_diff($userAnswers, $correctOptionIds);

        $isExactMatch = count($selectedCorrect) === count($correctOptionIds)
            && count($selectedIncorrect) === 0;

        if ($isExactMatch) {
            return ['is_correct' => true, 'partial' => 1];
        }

        // Calculate partial credit: correct selections minus incorrect selections
        $correctCount = count($selectedCorrect);
        $incorrectCount = count($selectedIncorrect);
        $totalCorrect = count($correctOptionIds);

        $partialScore = max(0, ($correctCount - $incorrectCount) / $totalCorrect);

        return [
            'is_correct' => false,
            'partial' => round($partialScore, 2),
        ];
    }

    /**
     * Grade a true/false question.
     */
    protected function gradeTrueFalse(Question $question, mixed $userAnswer): bool
    {
        if (empty($userAnswer)) {
            return false;
        }

        $correctOption = $question->correctOptions()->first();

        if (! $correctOption) {
            // Use metadata or option text to determine correct answer
            $trueOption = $question->options->first(fn ($o) => strtolower($o->option_text) === 'richtig' || strtolower($o->option_text) === 'true' || $o->is_correct);

            if ($trueOption) {
                return $userAnswer === ($trueOption->is_correct ? 'true' : 'false');
            }
        }

        return $correctOption && $correctOption->id === $userAnswer;
    }

    /**
     * Grade a matching question.
     */
    protected function gradeMatching(Question $question, mixed $userAnswer): array
    {
        $answers = is_array($userAnswer) ? $userAnswer : [];
        $metadata = $question->metadata ?? [];
        $correctMatches = $metadata['matches'] ?? [];

        if (empty($answers) || empty($correctMatches)) {
            return ['is_correct' => false, 'partial' => 0];
        }

        $correctCount = 0;
        $totalMatches = count($correctMatches);

        foreach ($correctMatches as $left => $right) {
            if (isset($answers[$left]) && $answers[$left] === $right) {
                $correctCount++;
            }
        }

        $isExactMatch = $correctCount === $totalMatches;
        $partialScore = $totalMatches > 0 ? $correctCount / $totalMatches : 0;

        return [
            'is_correct' => $isExactMatch,
            'partial' => round($partialScore, 2),
        ];
    }

    /**
     * Serialize answer for storage.
     */
    protected function serializeAnswer(mixed $answer): ?string
    {
        if ($answer === null) {
            return null;
        }

        if (is_array($answer)) {
            return json_encode($answer);
        }

        return (string) $answer;
    }

    /**
     * Update step progress after passing assessment.
     */
    protected function updateStepProgress(AssessmentAttempt $attempt): void
    {
        $step = $attempt->assessment->step;
        $enrollment = $attempt->enrollment;

        StepProgress::updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'step_id' => $step->id,
            ],
            [
                'status' => StepProgressStatus::Completed,
                'completed_at' => now(),
                'points_earned' => $attempt->points_earned,
            ]
        );

        // Recalculate enrollment progress
        $progressService = app(ProgressTrackingService::class);
        $progressService->recalculateEnrollmentProgress($enrollment);
    }

    /**
     * Get detailed results for an attempt.
     */
    public function getAttemptResults(AssessmentAttempt $attempt): array
    {
        $responses = $attempt->responses()->with('question.options')->get();
        $assessment = $attempt->assessment;

        $results = [];

        foreach ($responses as $response) {
            $question = $response->question;
            $userAnswer = json_decode($response->user_answer, true) ?? $response->user_answer;

            $results[] = [
                'question' => [
                    'id' => $question->id,
                    'type' => $question->question_type->value,
                    'text' => $question->question_text,
                    'explanation' => $question->explanation,
                    'points' => $question->points,
                ],
                'user_answer' => $userAnswer,
                'correct_answer' => $this->getCorrectAnswer($question),
                'is_correct' => $response->is_correct,
                'points_earned' => $response->points_earned,
                'options' => $assessment->show_correct_answers
                    ? $question->options->map(fn ($o) => [
                        'id' => $o->id,
                        'text' => $o->option_text,
                        'is_correct' => $o->is_correct,
                    ])->toArray()
                    : null,
            ];
        }

        return $results;
    }

    /**
     * Get the correct answer(s) for a question.
     */
    protected function getCorrectAnswer(Question $question): mixed
    {
        $correctOptions = $question->correctOptions();

        if ($question->question_type === QuestionType::MultipleChoice) {
            return $correctOptions->pluck('id')->toArray();
        }

        if ($question->question_type === QuestionType::Matching) {
            return $question->metadata['matches'] ?? [];
        }

        return $correctOptions->first()?->id;
    }
}
