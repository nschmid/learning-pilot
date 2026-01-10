<?php

namespace App\Livewire\Learner\Assessment;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Start extends Component
{
    public Assessment $assessment;

    public function mount(Assessment $assessment): void
    {
        $this->assessment = $assessment->load(['step.module.learningPath', 'questions']);
    }

    public function getEnrollmentProperty(): ?Enrollment
    {
        $path = $this->assessment->step->module->learningPath;

        return Enrollment::where('user_id', Auth::id())
            ->where('learning_path_id', $path->id)
            ->first();
    }

    public function getPreviousAttemptsProperty()
    {
        if (! $this->enrollment) {
            return collect();
        }

        return AssessmentAttempt::where('assessment_id', $this->assessment->id)
            ->where('enrollment_id', $this->enrollment->id)
            ->completed()
            ->orderBy('attempt_number', 'desc')
            ->get();
    }

    public function getAttemptsRemainingProperty(): ?int
    {
        if (! $this->assessment->hasAttemptLimit()) {
            return null;
        }

        $used = $this->previousAttempts->count();

        return max(0, $this->assessment->max_attempts - $used);
    }

    public function getCanStartProperty(): bool
    {
        if (! $this->enrollment) {
            return false;
        }

        if ($this->assessment->hasAttemptLimit() && $this->attemptsRemaining <= 0) {
            return false;
        }

        // Check if there's an in-progress attempt
        $inProgress = AssessmentAttempt::where('assessment_id', $this->assessment->id)
            ->where('enrollment_id', $this->enrollment->id)
            ->whereNull('completed_at')
            ->exists();

        return ! $inProgress;
    }

    public function getInProgressAttemptProperty(): ?AssessmentAttempt
    {
        if (! $this->enrollment) {
            return null;
        }

        return AssessmentAttempt::where('assessment_id', $this->assessment->id)
            ->where('enrollment_id', $this->enrollment->id)
            ->whereNull('completed_at')
            ->first();
    }

    public function getBestAttemptProperty(): ?AssessmentAttempt
    {
        return $this->previousAttempts->sortByDesc('score_percent')->first();
    }

    public function startAssessment(): void
    {
        if (! $this->canStart) {
            return;
        }

        $attemptNumber = $this->previousAttempts->count() + 1;

        $attempt = AssessmentAttempt::create([
            'assessment_id' => $this->assessment->id,
            'enrollment_id' => $this->enrollment->id,
            'attempt_number' => $attemptNumber,
            'started_at' => now(),
        ]);

        $this->redirect(
            route('learner.assessment.take', $this->assessment->id),
            navigate: true
        );
    }

    public function continueAssessment(): void
    {
        if (! $this->inProgressAttempt) {
            return;
        }

        $this->redirect(
            route('learner.assessment.take', $this->assessment->id),
            navigate: true
        );
    }

    public function render()
    {
        return view('livewire.learner.assessment.start')
            ->layout('layouts.learner', ['title' => $this->assessment->title]);
    }
}
