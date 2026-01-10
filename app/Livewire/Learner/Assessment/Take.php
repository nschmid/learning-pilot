<?php

namespace App\Livewire\Learner\Assessment;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Enrollment;
use App\Models\Question;
use App\Models\QuestionResponse;
use App\Services\AssessmentGradingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Take extends Component
{
    public Assessment $assessment;

    public AssessmentAttempt $attempt;

    public array $questions = [];

    public int $currentQuestionIndex = 0;

    public array $answers = [];

    public bool $isSubmitting = false;

    public function mount(Assessment $assessment): void
    {
        $this->assessment = $assessment;

        // Get enrollment
        $enrollment = $this->getEnrollment();
        if (! $enrollment) {
            $this->redirect(route('learner.assessment.start', $assessment->id), navigate: true);
            return;
        }

        // Get in-progress attempt
        $this->attempt = AssessmentAttempt::where('assessment_id', $assessment->id)
            ->where('enrollment_id', $enrollment->id)
            ->whereNull('completed_at')
            ->first();

        if (! $this->attempt) {
            $this->redirect(route('learner.assessment.start', $assessment->id), navigate: true);
            return;
        }

        // Load questions
        $this->loadQuestions();

        // Load any saved answers
        $this->loadSavedAnswers();
    }

    protected function getEnrollment(): ?Enrollment
    {
        $path = $this->assessment->step->module->learningPath;

        return Enrollment::where('user_id', Auth::id())
            ->where('learning_path_id', $path->id)
            ->first();
    }

    protected function loadQuestions(): void
    {
        $query = $this->assessment->questions()->with('options');

        if ($this->assessment->shuffle_questions) {
            $query->inRandomOrder();
        } else {
            $query->ordered();
        }

        $this->questions = $query->get()->map(function ($question) {
            $options = $question->options;

            if ($this->assessment->shuffle_answers && $question->hasOptions()) {
                $options = $options->shuffle();
            }

            return [
                'id' => $question->id,
                'type' => $question->question_type->value,
                'text' => $question->question_text,
                'image' => $question->question_image,
                'points' => $question->points,
                'options' => $options->map(fn ($o) => [
                    'id' => $o->id,
                    'text' => $o->option_text,
                ])->toArray(),
            ];
        })->toArray();

        // Initialize empty answers
        foreach ($this->questions as $question) {
            if (! isset($this->answers[$question['id']])) {
                $this->answers[$question['id']] = $question['type'] === 'multiple_choice' ? [] : null;
            }
        }
    }

    protected function loadSavedAnswers(): void
    {
        if ($this->attempt->answers) {
            $this->answers = array_merge($this->answers, $this->attempt->answers);
        }
    }

    #[Computed]
    public function currentQuestion(): ?array
    {
        return $this->questions[$this->currentQuestionIndex] ?? null;
    }

    #[Computed]
    public function totalQuestions(): int
    {
        return count($this->questions);
    }

    #[Computed]
    public function answeredCount(): int
    {
        return collect($this->answers)->filter(function ($answer) {
            if (is_array($answer)) {
                return count($answer) > 0;
            }
            return $answer !== null && $answer !== '';
        })->count();
    }

    #[Computed]
    public function timeRemaining(): ?int
    {
        return $this->attempt->timeRemaining();
    }

    #[Computed]
    public function isTimeUp(): bool
    {
        $remaining = $this->timeRemaining;
        return $remaining !== null && $remaining <= 0;
    }

    public function goToQuestion(int $index): void
    {
        if ($index >= 0 && $index < $this->totalQuestions) {
            $this->saveCurrentAnswer();
            $this->currentQuestionIndex = $index;
        }
    }

    public function nextQuestion(): void
    {
        $this->saveCurrentAnswer();
        if ($this->currentQuestionIndex < $this->totalQuestions - 1) {
            $this->currentQuestionIndex++;
        }
    }

    public function previousQuestion(): void
    {
        $this->saveCurrentAnswer();
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
    }

    public function selectOption(string $optionId): void
    {
        $question = $this->currentQuestion;
        if (! $question) {
            return;
        }

        if ($question['type'] === 'multiple_choice') {
            // Toggle selection for multiple choice
            $current = $this->answers[$question['id']] ?? [];
            if (in_array($optionId, $current)) {
                $this->answers[$question['id']] = array_values(array_diff($current, [$optionId]));
            } else {
                $this->answers[$question['id']][] = $optionId;
            }
        } else {
            // Single selection
            $this->answers[$question['id']] = $optionId;
        }
    }

    public function updateTextAnswer(string $value): void
    {
        $question = $this->currentQuestion;
        if ($question && $question['type'] === 'text') {
            $this->answers[$question['id']] = $value;
        }
    }

    protected function saveCurrentAnswer(): void
    {
        // Save answers to the attempt periodically
        $this->attempt->update(['answers' => $this->answers]);
    }

    public function submitAssessment(): void
    {
        $this->isSubmitting = true;
        $this->saveCurrentAnswer();

        // Use grading service
        $gradingService = app(AssessmentGradingService::class);
        $result = $gradingService->gradeAttempt($this->attempt, $this->answers);

        $this->redirect(
            route('learner.assessment.result', [$this->assessment->id, $this->attempt->id]),
            navigate: true
        );
    }

    public function isAnswered(string $questionId): bool
    {
        $answer = $this->answers[$questionId] ?? null;
        if (is_array($answer)) {
            return count($answer) > 0;
        }
        return $answer !== null && $answer !== '';
    }

    public function isOptionSelected(string $optionId): bool
    {
        $question = $this->currentQuestion;
        if (! $question) {
            return false;
        }

        $answer = $this->answers[$question['id']] ?? null;

        if ($question['type'] === 'multiple_choice') {
            return is_array($answer) && in_array($optionId, $answer);
        }

        return $answer === $optionId;
    }

    public function render()
    {
        return view('livewire.learner.assessment.take')
            ->layout('layouts.blank', ['title' => $this->assessment->title]);
    }
}
