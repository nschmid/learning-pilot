<?php

namespace App\Livewire\Learner\Assessment;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Services\AssessmentGradingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Result extends Component
{
    public Assessment $assessment;

    public AssessmentAttempt $attempt;

    public array $results = [];

    public function mount(Assessment $assessment, AssessmentAttempt $attempt): void
    {
        $this->assessment = $assessment->load('step.module.learningPath');
        $this->attempt = $attempt;

        // Verify this attempt belongs to the user
        if ($this->attempt->enrollment->user_id !== Auth::id()) {
            abort(403);
        }

        // Load detailed results
        $gradingService = app(AssessmentGradingService::class);
        $this->results = $gradingService->getAttemptResults($this->attempt);
    }

    public function getStatsProperty(): array
    {
        $totalQuestions = count($this->results);
        $correctAnswers = collect($this->results)->where('is_correct', true)->count();
        $incorrectAnswers = collect($this->results)->where('is_correct', false)->count();
        $pendingReview = collect($this->results)->whereNull('is_correct')->count();

        return [
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'incorrect_answers' => $incorrectAnswers,
            'pending_review' => $pendingReview,
            'accuracy' => $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 1) : 0,
        ];
    }

    public function retryAssessment(): void
    {
        $this->redirect(
            route('learner.assessment.start', $this->assessment->id),
            navigate: true
        );
    }

    public function continueLearning(): void
    {
        $this->redirect(
            route('learner.learn.index', $this->assessment->step->module->learningPath->slug),
            navigate: true
        );
    }

    public function render()
    {
        return view('livewire.learner.assessment.result')
            ->layout('layouts.learner', ['title' => __('Testergebnis')]);
    }
}
