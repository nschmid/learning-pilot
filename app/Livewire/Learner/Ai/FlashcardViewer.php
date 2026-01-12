<?php

namespace App\Livewire\Learner\Ai;

use App\Models\AiGeneratedContent;
use App\Models\Module;
use App\Services\AI\AISummaryService;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.learner')]
#[Title('Lernkarten - LearningPilot')]
class FlashcardViewer extends Component
{
    public ?string $moduleId = null;
    public array $flashcards = [];
    public int $currentIndex = 0;
    public bool $isFlipped = false;
    public bool $isLoading = false;
    public bool $isGenerating = false;

    // SM-2 algorithm tracking
    public array $cardProgress = [];

    public function mount(string $module): void
    {
        $this->moduleId = $module;
        $this->loadFlashcards();
    }

    public function loadFlashcards(): void
    {
        $module = Module::find($this->moduleId);

        if (!$module) {
            return;
        }

        // Try to load existing flashcards
        $content = AiGeneratedContent::where('contentable_type', Module::class)
            ->where('contentable_id', $this->moduleId)
            ->where('content_type', 'flashcards')
            ->where('user_id', auth()->id())
            ->latest()
            ->first();

        if ($content) {
            $this->flashcards = $content->content['cards'] ?? [];
            $this->initializeProgress();
        }
    }

    public function generateFlashcards(): void
    {
        $this->isGenerating = true;

        try {
            $module = Module::findOrFail($this->moduleId);
            $summaryService = app(AISummaryService::class);

            $result = $summaryService->generateFlashcards(
                module: $module,
                user: auth()->user(),
            );

            // Parse flashcards from the generated content
            $content = $result->content;
            $this->flashcards = is_array($content) ? ($content['cards'] ?? $summaryService->parseFlashcards($result)) : [];
            $this->currentIndex = 0;
            $this->initializeProgress();

        } catch (\Exception $e) {
            session()->flash('error', __('Fehler beim Generieren der Lernkarten: :message', ['message' => $e->getMessage()]));
        } finally {
            $this->isGenerating = false;
        }
    }

    protected function initializeProgress(): void
    {
        $this->cardProgress = [];

        foreach ($this->flashcards as $index => $card) {
            $this->cardProgress[$index] = [
                'easeFactor' => 2.5,
                'interval' => 0,
                'repetitions' => 0,
                'nextReview' => now(),
            ];
        }
    }

    public function flipCard(): void
    {
        $this->isFlipped = !$this->isFlipped;
    }

    public function markCard(string $rating): void
    {
        // SM-2 Algorithm implementation
        $grade = match ($rating) {
            'again' => 0,
            'hard' => 2,
            'good' => 3,
            'easy' => 5,
            default => 3,
        };

        $progress = $this->cardProgress[$this->currentIndex] ?? [
            'easeFactor' => 2.5,
            'interval' => 0,
            'repetitions' => 0,
        ];

        if ($grade >= 3) {
            // Correct response
            if ($progress['repetitions'] === 0) {
                $progress['interval'] = 1;
            } elseif ($progress['repetitions'] === 1) {
                $progress['interval'] = 6;
            } else {
                $progress['interval'] = round($progress['interval'] * $progress['easeFactor']);
            }
            $progress['repetitions']++;
        } else {
            // Incorrect response
            $progress['repetitions'] = 0;
            $progress['interval'] = 1;
        }

        // Update ease factor
        $progress['easeFactor'] = max(1.3, $progress['easeFactor'] + (0.1 - (5 - $grade) * (0.08 + (5 - $grade) * 0.02)));
        $progress['nextReview'] = now()->addDays($progress['interval']);

        $this->cardProgress[$this->currentIndex] = $progress;

        // Move to next card
        $this->nextCard();
    }

    public function nextCard(): void
    {
        $this->isFlipped = false;

        if ($this->currentIndex < count($this->flashcards) - 1) {
            $this->currentIndex++;
        } else {
            $this->currentIndex = 0; // Loop back to start
        }
    }

    public function previousCard(): void
    {
        $this->isFlipped = false;

        if ($this->currentIndex > 0) {
            $this->currentIndex--;
        } else {
            $this->currentIndex = count($this->flashcards) - 1;
        }
    }

    public function goToCard(int $index): void
    {
        if ($index >= 0 && $index < count($this->flashcards)) {
            $this->currentIndex = $index;
            $this->isFlipped = false;
        }
    }

    public function getCurrentCardProperty(): ?array
    {
        return $this->flashcards[$this->currentIndex] ?? null;
    }

    public function getModuleProperty(): ?Module
    {
        return $this->moduleId ? Module::find($this->moduleId) : null;
    }

    public function getProgressStatsProperty(): array
    {
        $total = count($this->flashcards);
        $reviewed = 0;
        $mastered = 0;

        foreach ($this->cardProgress as $progress) {
            if ($progress['repetitions'] > 0) {
                $reviewed++;
            }
            if ($progress['interval'] >= 21) {
                $mastered++;
            }
        }

        return [
            'total' => $total,
            'reviewed' => $reviewed,
            'mastered' => $mastered,
            'percent' => $total > 0 ? round(($reviewed / $total) * 100) : 0,
        ];
    }

    public function render()
    {
        return view('livewire.learner.ai.flashcard-viewer');
    }
}
