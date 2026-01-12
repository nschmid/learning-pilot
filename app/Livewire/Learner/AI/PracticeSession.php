<?php

namespace App\Livewire\Learner\AI;

use App\Enums\AIPracticeDifficulty;
use App\Enums\Difficulty;
use App\Models\AIPracticeQuestion;
use App\Models\AIPracticeSession as PracticeSessionModel;
use App\Models\Module;
use App\Services\AI\AIPracticeGeneratorService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.learner')]
#[Title('KI-Übungen - LearningPilot')]
class PracticeSession extends Component
{
    public ?string $moduleId = null;
    public string $difficulty = 'intermediate';
    public int $questionCount = 5;

    public ?string $sessionId = null;
    public int $currentIndex = 0;
    public ?array $currentQuestion = null;
    public ?string $userAnswer = null;
    public bool $showResult = false;
    public bool $isCorrect = false;
    public ?string $explanation = null;
    public bool $isLoading = false;
    public bool $sessionComplete = false;

    public function mount(string $module): void
    {
        $this->moduleId = $module;
    }

    public function startSession(): void
    {
        $this->validate([
            'difficulty' => 'required|in:beginner,intermediate,advanced,adaptive',
            'questionCount' => 'required|integer|min:3|max:20',
        ]);

        $this->isLoading = true;

        try {
            $module = Module::findOrFail($this->moduleId);
            $practiceService = app(AIPracticeGeneratorService::class);

            // Map AIPracticeDifficulty to Difficulty enum (adaptive uses null for path default)
            $difficulty = match ($this->difficulty) {
                'beginner' => Difficulty::Beginner,
                'intermediate' => Difficulty::Intermediate,
                'advanced' => Difficulty::Advanced,
                default => null, // adaptive - use path's default difficulty
            };

            $session = $practiceService->startSession(
                user: auth()->user(),
                path: $module->learningPath,
                module: $module,
                difficulty: $difficulty,
                questionCount: $this->questionCount,
            );

            $this->sessionId = $session->id;
            $this->currentIndex = 0;
            $this->loadCurrentQuestion();

        } catch (\Exception $e) {
            session()->flash('error', __('Fehler beim Generieren der Übungsfragen: :message', ['message' => $e->getMessage()]));
        } finally {
            $this->isLoading = false;
        }
    }

    public function submitAnswer(): void
    {
        if (!$this->userAnswer || !$this->currentQuestion) {
            return;
        }

        $question = AIPracticeQuestion::find($this->currentQuestion['id']);

        if (!$question) {
            return;
        }

        $this->isCorrect = $this->checkAnswer($question, $this->userAnswer);
        $this->explanation = $question->explanation;
        $this->showResult = true;

        // Update question record
        $question->update([
            'user_answer' => $this->userAnswer,
            'is_correct' => $this->isCorrect,
            'answered_at' => now(),
        ]);

        // Update session score
        $this->updateSessionScore();
    }

    public function nextQuestion(): void
    {
        $this->currentIndex++;
        $this->userAnswer = null;
        $this->showResult = false;
        $this->isCorrect = false;
        $this->explanation = null;

        $this->loadCurrentQuestion();
    }

    protected function loadCurrentQuestion(): void
    {
        $session = PracticeSessionModel::find($this->sessionId);

        if (!$session) {
            return;
        }

        $questions = $session->questions()->orderBy('position')->get();

        if ($this->currentIndex >= $questions->count()) {
            $this->sessionComplete = true;
            $this->currentQuestion = null;
            return;
        }

        $question = $questions[$this->currentIndex];

        $this->currentQuestion = [
            'id' => $question->id,
            'text' => $question->question_text,
            'type' => $question->question_type,
            'options' => $question->options,
            'position' => $this->currentIndex + 1,
            'total' => $questions->count(),
        ];
    }

    protected function checkAnswer(AIPracticeQuestion $question, string $answer): bool
    {
        $correctAnswer = $question->correct_answer;

        // Handle different question types
        if ($question->question_type === 'multiple_choice' || $question->question_type === 'single_choice') {
            return strtolower(trim($answer)) === strtolower(trim($correctAnswer));
        }

        if ($question->question_type === 'true_false') {
            return strtolower(trim($answer)) === strtolower(trim($correctAnswer));
        }

        // For text questions, do a fuzzy match
        return similar_text(strtolower($answer), strtolower($correctAnswer)) > 80;
    }

    protected function updateSessionScore(): void
    {
        $session = PracticeSessionModel::find($this->sessionId);

        if (!$session) {
            return;
        }

        $totalQuestions = $session->questions()->count();
        $correctAnswers = $session->questions()->where('is_correct', true)->count();

        $session->update([
            'score_percent' => $totalQuestions > 0
                ? round(($correctAnswers / $totalQuestions) * 100)
                : 0,
        ]);
    }

    public function getModuleProperty(): ?Module
    {
        return $this->moduleId ? Module::find($this->moduleId) : null;
    }

    public function getSessionProperty(): ?PracticeSessionModel
    {
        return $this->sessionId ? PracticeSessionModel::find($this->sessionId) : null;
    }

    public function getDifficultiesProperty(): array
    {
        return [
            'beginner' => AIPracticeDifficulty::Beginner->label(),
            'intermediate' => AIPracticeDifficulty::Intermediate->label(),
            'advanced' => AIPracticeDifficulty::Advanced->label(),
            'adaptive' => AIPracticeDifficulty::Adaptive->label(),
        ];
    }

    public function render()
    {
        return view('livewire.learner.ai.practice-session');
    }
}
