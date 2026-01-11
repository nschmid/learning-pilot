<?php

namespace App\Livewire\Learner\AI;

use App\Models\Question;
use App\Services\AI\AIExplanationService;
use Livewire\Attributes\On;
use Livewire\Component;

class ExplanationModal extends Component
{
    public bool $show = false;
    public ?string $questionId = null;
    public ?string $userAnswer = null;
    public ?string $explanation = null;
    public bool $isLoading = false;

    #[On('show-explanation')]
    public function showExplanation(string $questionId, string $userAnswer): void
    {
        $this->questionId = $questionId;
        $this->userAnswer = $userAnswer;
        $this->explanation = null;
        $this->show = true;
        $this->generateExplanation();
    }

    public function generateExplanation(): void
    {
        $this->isLoading = true;

        try {
            $question = Question::findOrFail($this->questionId);
            $explanationService = app(AIExplanationService::class);

            $this->explanation = $explanationService->generateExplanation(
                user: auth()->user(),
                question: $question,
                userAnswer: $this->userAnswer,
            );

        } catch (\Exception $e) {
            $this->explanation = __('Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.');
        } finally {
            $this->isLoading = false;
        }
    }

    public function close(): void
    {
        $this->show = false;
        $this->questionId = null;
        $this->userAnswer = null;
        $this->explanation = null;
    }

    public function getQuestionProperty(): ?Question
    {
        return $this->questionId ? Question::find($this->questionId) : null;
    }

    public function render()
    {
        return view('livewire.learner.ai.explanation-modal');
    }
}
