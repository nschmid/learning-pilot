<?php

namespace App\Livewire\Learner\Ai;

use App\Exceptions\AIQuotaExceededException;
use App\Models\StepProgress;
use App\Services\AI\AIExplanationService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class HintButton extends Component
{
    public string $stepProgressId;

    public int $currentHintLevel = 0;

    public int $maxHintLevel = 4;

    public array $hints = [];

    public bool $isLoading = false;

    public ?string $error = null;

    public function mount(string $stepProgressId): void
    {
        $this->stepProgressId = $stepProgressId;
        $this->loadExistingHints();
    }

    #[Computed]
    public function stepProgress(): ?StepProgress
    {
        return StepProgress::find($this->stepProgressId);
    }

    #[Computed]
    public function canRequestHint(): bool
    {
        return $this->currentHintLevel < $this->maxHintLevel && ! $this->isLoading;
    }

    #[Computed]
    public function nextHintLevel(): int
    {
        return min($this->currentHintLevel + 1, $this->maxHintLevel);
    }

    public function requestHint(): void
    {
        if (! $this->canRequestHint) {
            return;
        }

        $this->isLoading = true;
        $this->error = null;

        try {
            $progress = $this->stepProgress;

            if (! $progress) {
                throw new \Exception(__('Lernfortschritt nicht gefunden.'));
            }

            $explanationService = app(AIExplanationService::class);

            $hintContent = $explanationService->generateHint(
                $progress,
                auth()->user(),
                $this->nextHintLevel
            );

            $this->currentHintLevel = $this->nextHintLevel;
            $this->hints[] = [
                'level' => $this->currentHintLevel,
                'content' => $hintContent->content,
                'generated_at' => now()->toISOString(),
            ];

        } catch (AIQuotaExceededException $e) {
            $this->error = $e->getMessage();
        } catch (\Exception $e) {
            $this->error = __('Es ist ein Fehler aufgetreten. Bitte versuchen Sie es später erneut.');
        } finally {
            $this->isLoading = false;
        }
    }

    public function clearHints(): void
    {
        $this->hints = [];
        $this->currentHintLevel = 0;
        $this->error = null;
    }

    protected function loadExistingHints(): void
    {
        $progress = $this->stepProgress;

        if (! $progress) {
            return;
        }

        // Load previously generated hints from cache
        $cachedHints = $progress->aiGeneratedContents()
            ->where('content_type', 'hint')
            ->orderBy('created_at')
            ->get();

        foreach ($cachedHints as $hint) {
            $level = $hint->content_metadata['hint_level'] ?? 1;
            $this->hints[] = [
                'level' => $level,
                'content' => $hint->content,
                'generated_at' => $hint->created_at->toISOString(),
            ];
            $this->currentHintLevel = max($this->currentHintLevel, $level);
        }
    }

    public function render()
    {
        return view('livewire.learner.ai.hint-button');
    }
}
