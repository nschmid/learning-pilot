<?php

namespace App\Livewire\Learner\Learn;

use App\Enums\StepProgressStatus;
use App\Models\Enrollment;
use App\Models\LearningPath;
use App\Models\LearningStep;
use App\Models\StepProgress;
use App\Services\ProgressTrackingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class StepViewer extends Component
{
    public LearningPath $path;

    public ?LearningStep $currentStep = null;

    public int $stepNumber = 1;

    public bool $showSidebar = true;

    protected $startTime;

    public function mount(LearningPath $path, ?int $step = null): void
    {
        $this->path = $path->load([
            'modules' => fn ($q) => $q->ordered(),
            'modules.steps' => fn ($q) => $q->ordered(),
        ]);

        // Find the step to show
        if ($step) {
            $this->stepNumber = $step;
        } else {
            // Find the last incomplete step or the first step
            $this->stepNumber = $this->findCurrentStepNumber();
        }

        $this->loadStep();
        $this->markStepInProgress();
    }

    protected function findCurrentStepNumber(): int
    {
        $enrollment = $this->enrollment;
        if (! $enrollment) {
            return 1;
        }

        $allSteps = $this->allSteps;
        $completedStepIds = $enrollment->stepProgress()
            ->where('status', StepProgressStatus::Completed)
            ->pluck('step_id')
            ->toArray();

        foreach ($allSteps as $index => $step) {
            if (! in_array($step->id, $completedStepIds)) {
                return $index + 1;
            }
        }

        return 1;
    }

    protected function loadStep(): void
    {
        $allSteps = $this->allSteps;
        $index = max(0, min($this->stepNumber - 1, count($allSteps) - 1));

        $this->currentStep = $allSteps[$index] ?? null;

        if ($this->currentStep) {
            $this->currentStep->load(['materials', 'task', 'assessment']);
        }
    }

    protected function markStepInProgress(): void
    {
        if (! $this->currentStep || ! $this->enrollment) {
            return;
        }

        $progressService = app(ProgressTrackingService::class);
        $progressService->startStep($this->enrollment, $this->currentStep);
    }

    #[Computed]
    public function enrollment(): ?Enrollment
    {
        return Enrollment::where('user_id', Auth::id())
            ->where('learning_path_id', $this->path->id)
            ->first();
    }

    #[Computed]
    public function allSteps(): array
    {
        $steps = [];
        foreach ($this->path->modules as $module) {
            foreach ($module->steps as $step) {
                $steps[] = $step;
            }
        }

        return $steps;
    }

    #[Computed]
    public function totalSteps(): int
    {
        return count($this->allSteps);
    }

    #[Computed]
    public function progress(): float
    {
        if (! $this->enrollment || $this->totalSteps === 0) {
            return 0;
        }

        $completed = $this->enrollment->stepProgress()
            ->where('status', StepProgressStatus::Completed)
            ->count();

        return round(($completed / $this->totalSteps) * 100, 1);
    }

    #[Computed]
    public function completedStepIds(): array
    {
        if (! $this->enrollment) {
            return [];
        }

        return $this->enrollment->stepProgress()
            ->where('status', StepProgressStatus::Completed)
            ->pluck('step_id')
            ->toArray();
    }

    public function isStepCompleted(string $stepId): bool
    {
        return in_array($stepId, $this->completedStepIds);
    }

    public function goToStep(int $stepNumber): void
    {
        $this->stepNumber = max(1, min($stepNumber, $this->totalSteps));
        $this->loadStep();
        $this->markStepInProgress();

        $this->dispatch('step-changed');
    }

    public function nextStep(): void
    {
        if ($this->stepNumber < $this->totalSteps) {
            $this->goToStep($this->stepNumber + 1);
        }
    }

    public function previousStep(): void
    {
        if ($this->stepNumber > 1) {
            $this->goToStep($this->stepNumber - 1);
        }
    }

    public function completeStep(): void
    {
        if (! $this->currentStep || ! $this->enrollment) {
            return;
        }

        $progressService = app(ProgressTrackingService::class);
        $progressService->completeStep($this->enrollment, $this->currentStep);

        // Clear computed properties cache
        unset($this->completedStepIds);
        unset($this->progress);

        $this->dispatch('step-completed', stepId: $this->currentStep->id);

        // Auto-advance to next step
        if ($this->stepNumber < $this->totalSteps) {
            $this->nextStep();
        }
    }

    public function toggleSidebar(): void
    {
        $this->showSidebar = ! $this->showSidebar;
    }

    public function render()
    {
        return view('livewire.learner.learn.step-viewer')
            ->layout('layouts.blank', [
                'title' => $this->currentStep?->title ?? $this->path->title,
            ]);
    }
}
