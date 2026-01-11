<?php

namespace App\Livewire\Learner\AI;

use App\Models\AITutorConversation;
use App\Models\AITutorMessage;
use App\Models\LearningStep;
use App\Services\AI\AITutorService;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.learner')]
#[Title('KI-Tutor - LearningPilot')]
class TutorChat extends Component
{
    public string $message = '';
    public ?string $conversationId = null;
    public ?string $stepId = null;
    public bool $isTyping = false;

    protected $listeners = ['sendMessage'];

    public function mount(?string $step = null, ?string $conversation = null): void
    {
        $this->stepId = $step;
        $this->conversationId = $conversation;

        // Load existing conversation if provided
        if ($conversation) {
            $conv = AITutorConversation::find($conversation);
            if ($conv && $conv->user_id === auth()->id()) {
                $this->conversationId = $conv->id;
                $this->stepId = $conv->context_type === LearningStep::class
                    ? $conv->context_id
                    : null;
            }
        }
    }

    public function sendMessage(): void
    {
        $this->validate([
            'message' => 'required|string|min:1|max:2000',
        ]);

        $user = auth()->user();
        $this->isTyping = true;

        try {
            $tutorService = app(AITutorService::class);

            // Get or create conversation
            $conversation = $this->getOrCreateConversation();

            // Get step context if available
            $step = $this->stepId ? LearningStep::find($this->stepId) : null;

            // Send message to AI
            $response = $tutorService->sendMessage(
                user: $user,
                message: $this->message,
                conversationId: $conversation->id,
                contextType: $step ? get_class($step) : null,
                contextId: $this->stepId,
            );

            $this->conversationId = $conversation->id;
            $this->message = '';

        } catch (\Exception $e) {
            session()->flash('error', __('Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.'));
        } finally {
            $this->isTyping = false;
        }
    }

    public function startNewConversation(): void
    {
        $this->conversationId = null;
        $this->message = '';
    }

    public function loadConversation(string $id): void
    {
        $conversation = AITutorConversation::find($id);

        if ($conversation && $conversation->user_id === auth()->id()) {
            $this->conversationId = $conversation->id;
            $this->stepId = $conversation->context_type === LearningStep::class
                ? $conversation->context_id
                : null;
        }
    }

    protected function getOrCreateConversation(): AITutorConversation
    {
        if ($this->conversationId) {
            return AITutorConversation::findOrFail($this->conversationId);
        }

        $step = $this->stepId ? LearningStep::find($this->stepId) : null;

        return AITutorConversation::create([
            'user_id' => auth()->id(),
            'context_type' => $step ? get_class($step) : null,
            'context_id' => $this->stepId,
            'title' => $step
                ? __('Konversation zu: :title', ['title' => $step->title])
                : __('Allgemeine Konversation'),
        ]);
    }

    public function getMessagesProperty(): Collection
    {
        if (!$this->conversationId) {
            return collect();
        }

        return AITutorMessage::where('conversation_id', $this->conversationId)
            ->orderBy('created_at')
            ->get();
    }

    public function getConversationsProperty(): Collection
    {
        return AITutorConversation::where('user_id', auth()->id())
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get();
    }

    public function getStepProperty(): ?LearningStep
    {
        return $this->stepId ? LearningStep::find($this->stepId) : null;
    }

    public function render()
    {
        return view('livewire.learner.ai.tutor-chat');
    }
}
