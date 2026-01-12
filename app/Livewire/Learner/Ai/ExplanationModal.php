<?php

namespace App\Livewire\Learner\Ai;

use App\Models\QuestionResponse;
use App\Services\AI\AIExplanationService;
use Livewire\Attributes\On;
use Livewire\Component;

class ExplanationModal extends Component
{
    public bool $show = false;
    public ?string $responseId = null;
    public ?string $explanation = null;
    public bool $isLoading = false;

    #[On('show-explanation')]
    public function showExplanation(string $responseId): void
    {
        $this->responseId = $responseId;
        $this->explanation = null;
        $this->show = true;
        $this->generateExplanation();
    }

    public function generateExplanation(): void
    {
        $this->isLoading = true;

        try {
            $response = QuestionResponse::findOrFail($this->responseId);
            $explanationService = app(AIExplanationService::class);

            $result = $explanationService->generateExplanation($response, auth()->user());
            // Content is stored as array, extract text if present
            $content = $result->content;
            $this->explanation = is_array($content) ? ($content['text'] ?? json_encode($content, JSON_PRETTY_PRINT)) : (string) $content;

        } catch (\Exception $e) {
            $this->explanation = __('Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.');
        } finally {
            $this->isLoading = false;
        }
    }

    public function close(): void
    {
        $this->show = false;
        $this->responseId = null;
        $this->explanation = null;
    }

    public function getResponseProperty(): ?QuestionResponse
    {
        return $this->responseId ? QuestionResponse::with('question')->find($this->responseId) : null;
    }

    public function render()
    {
        return view('livewire.learner.ai.explanation-modal');
    }
}
