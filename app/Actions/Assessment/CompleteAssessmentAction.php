<?php

namespace App\Actions\Assessment;

use App\Events\AssessmentCompleted;
use App\Events\AssessmentFailed;
use App\Events\AssessmentPassed;
use App\Models\AssessmentAttempt;
use App\Models\QuestionResponse;
use App\Services\AssessmentGradingService;

class CompleteAssessmentAction
{
    public function __construct(
        protected AssessmentGradingService $gradingService
    ) {}

    /**
     * Complete an assessment attempt and calculate the score.
     */
    public function execute(AssessmentAttempt $attempt, array $answers): AssessmentAttempt
    {
        $assessment = $attempt->assessment;
        $totalPoints = 0;
        $earnedPoints = 0;

        // Process each answer
        foreach ($assessment->questions as $question) {
            $totalPoints += $question->points;
            $userAnswer = $answers[$question->id] ?? null;

            // Grade the answer
            $result = $this->gradingService->gradeQuestion($question, $userAnswer);

            // Save the response
            QuestionResponse::create([
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'user_answer' => is_array($userAnswer) ? json_encode($userAnswer) : $userAnswer,
                'is_correct' => $result['is_correct'],
                'points_earned' => $result['points'],
            ]);

            $earnedPoints += $result['points'];
        }

        // Calculate score percentage
        $scorePercent = $totalPoints > 0 ? ($earnedPoints / $totalPoints) * 100 : 0;
        $passed = $scorePercent >= $assessment->passing_score_percent;

        // Calculate time spent
        $timeSpent = now()->diffInSeconds($attempt->started_at);

        // Update attempt
        $attempt->update([
            'completed_at' => now(),
            'score_percent' => round($scorePercent, 2),
            'points_earned' => $earnedPoints,
            'passed' => $passed,
            'time_spent_seconds' => $timeSpent,
            'answers' => $answers,
        ]);

        // Dispatch events
        event(new AssessmentCompleted($attempt));

        if ($passed) {
            event(new AssessmentPassed($attempt));
        } else {
            event(new AssessmentFailed($attempt));
        }

        activity()
            ->performedOn($attempt)
            ->causedBy($attempt->enrollment->user)
            ->withProperties([
                'score' => $scorePercent,
                'passed' => $passed,
            ])
            ->log('completed assessment');

        return $attempt->fresh();
    }
}
