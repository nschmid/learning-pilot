<?php

namespace App\Livewire\Instructor\Assessments;

use App\Enums\AssessmentType;
use App\Enums\QuestionType;
use App\Models\AnswerOption;
use App\Models\Assessment;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class AssessmentBuilder extends Component
{
    public Assessment $assessment;

    // Assessment settings
    public string $title = '';
    public string $description = '';
    public string $instructions = '';
    public string $assessmentType = 'quiz';
    public ?int $timeLimit = null;
    public int $passingScore = 70;
    public ?int $maxAttempts = 3;
    public bool $shuffleQuestions = false;
    public bool $shuffleAnswers = false;
    public bool $showCorrectAnswers = true;
    public bool $showScoreImmediately = true;

    // Question modal
    public bool $showQuestionModal = false;
    public ?string $editingQuestionId = null;
    public string $questionType = 'single_choice';
    public string $questionText = '';
    public string $questionExplanation = '';
    public int $questionPoints = 10;
    public array $questionOptions = [];

    // Import modal
    public bool $showImportModal = false;
    public string $importText = '';

    public function mount(Assessment $assessment): void
    {
        $path = $assessment->step->module->learningPath;
        if ($path->creator_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $this->assessment = $assessment;
        $this->loadAssessmentData();
    }

    protected function loadAssessmentData(): void
    {
        $this->title = $this->assessment->title;
        $this->description = $this->assessment->description ?? '';
        $this->instructions = $this->assessment->instructions ?? '';
        $this->assessmentType = $this->assessment->assessment_type?->value ?? 'quiz';
        $this->timeLimit = $this->assessment->time_limit_minutes;
        $this->passingScore = $this->assessment->passing_score_percent;
        $this->maxAttempts = $this->assessment->max_attempts;
        $this->shuffleQuestions = $this->assessment->shuffle_questions;
        $this->shuffleAnswers = $this->assessment->shuffle_answers;
        $this->showCorrectAnswers = $this->assessment->show_correct_answers;
        $this->showScoreImmediately = $this->assessment->show_score_immediately;
    }

    #[Computed]
    public function questions()
    {
        return $this->assessment->questions()->with('options')->ordered()->get();
    }

    #[Computed]
    public function totalPoints(): int
    {
        return $this->questions->sum('points');
    }

    #[Computed]
    public function questionCount(): int
    {
        return $this->questions->count();
    }

    public function saveSettings(): void
    {
        $this->validate([
            'title' => ['required', 'string', 'min:2', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'instructions' => ['nullable', 'string', 'max:5000'],
            'assessmentType' => ['required', 'in:quiz,exam,survey'],
            'timeLimit' => ['nullable', 'integer', 'min:1', 'max:480'],
            'passingScore' => ['required', 'integer', 'min:0', 'max:100'],
            'maxAttempts' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $this->assessment->update([
            'title' => $this->title,
            'description' => $this->description ?: null,
            'instructions' => $this->instructions ?: null,
            'assessment_type' => AssessmentType::from($this->assessmentType),
            'time_limit_minutes' => $this->timeLimit,
            'passing_score_percent' => $this->passingScore,
            'max_attempts' => $this->maxAttempts,
            'shuffle_questions' => $this->shuffleQuestions,
            'shuffle_answers' => $this->shuffleAnswers,
            'show_correct_answers' => $this->showCorrectAnswers,
            'show_score_immediately' => $this->showScoreImmediately,
        ]);

        session()->flash('success', __('Einstellungen gespeichert.'));
    }

    // Question methods
    public function openQuestionModal(?string $questionId = null): void
    {
        if ($questionId) {
            $question = Question::with('options')->find($questionId);
            $this->editingQuestionId = $questionId;
            $this->questionType = $question->question_type->value;
            $this->questionText = $question->question_text;
            $this->questionExplanation = $question->explanation ?? '';
            $this->questionPoints = $question->points;
            $this->questionOptions = $question->options->map(fn($o) => [
                'id' => $o->id,
                'text' => $o->option_text,
                'is_correct' => $o->is_correct,
                'feedback' => $o->feedback ?? '',
            ])->toArray();
        } else {
            $this->resetQuestionForm();
        }
        $this->showQuestionModal = true;
    }

    public function closeQuestionModal(): void
    {
        $this->showQuestionModal = false;
        $this->resetQuestionForm();
    }

    protected function resetQuestionForm(): void
    {
        $this->editingQuestionId = null;
        $this->questionType = 'single_choice';
        $this->questionText = '';
        $this->questionExplanation = '';
        $this->questionPoints = 10;
        $this->questionOptions = [
            ['id' => null, 'text' => '', 'is_correct' => false, 'feedback' => ''],
            ['id' => null, 'text' => '', 'is_correct' => false, 'feedback' => ''],
        ];
    }

    public function addOption(): void
    {
        if (count($this->questionOptions) < 10) {
            $this->questionOptions[] = ['id' => null, 'text' => '', 'is_correct' => false, 'feedback' => ''];
        }
    }

    public function removeOption(int $index): void
    {
        if (count($this->questionOptions) > 2) {
            unset($this->questionOptions[$index]);
            $this->questionOptions = array_values($this->questionOptions);
        }
    }

    public function setCorrectOption(int $index): void
    {
        if ($this->questionType === 'single_choice') {
            foreach ($this->questionOptions as $i => $option) {
                $this->questionOptions[$i]['is_correct'] = ($i === $index);
            }
        }
    }

    public function saveQuestion(): void
    {
        $hasOptions = in_array($this->questionType, ['single_choice', 'multiple_choice', 'true_false']);

        $rules = [
            'questionText' => ['required', 'string', 'min:5', 'max:5000'],
            'questionType' => ['required', 'in:single_choice,multiple_choice,true_false,text,matching'],
            'questionPoints' => ['required', 'integer', 'min:1', 'max:100'],
        ];

        if ($hasOptions) {
            $rules['questionOptions'] = ['required', 'array', 'min:2'];
            $rules['questionOptions.*.text'] = ['required', 'string', 'max:1000'];
        }

        $this->validate($rules);

        // Validate at least one correct answer for choice questions
        if ($hasOptions) {
            $hasCorrect = collect($this->questionOptions)->contains(fn($o) => $o['is_correct']);
            if (!$hasCorrect) {
                $this->addError('questionOptions', __('Bitte markiere mindestens eine richtige Antwort.'));
                return;
            }
        }

        $data = [
            'assessment_id' => $this->assessment->id,
            'question_type' => QuestionType::from($this->questionType),
            'question_text' => $this->questionText,
            'explanation' => $this->questionExplanation ?: null,
            'points' => $this->questionPoints,
        ];

        if ($this->editingQuestionId) {
            $question = Question::find($this->editingQuestionId);
            $question->update($data);
        } else {
            $maxPosition = $this->assessment->questions()->max('position') ?? 0;
            $data['position'] = $maxPosition + 1;
            $question = Question::create($data);
        }

        // Save options
        if ($hasOptions) {
            $existingIds = collect($this->questionOptions)->pluck('id')->filter()->toArray();
            $question->options()->whereNotIn('id', $existingIds)->delete();

            foreach ($this->questionOptions as $index => $option) {
                if (!empty($option['text'])) {
                    AnswerOption::updateOrCreate(
                        ['id' => $option['id'] ?? null],
                        [
                            'question_id' => $question->id,
                            'option_text' => $option['text'],
                            'is_correct' => $option['is_correct'] ?? false,
                            'feedback' => $option['feedback'] ?? null,
                            'position' => $index + 1,
                        ]
                    );
                }
            }
        } else {
            $question->options()->delete();
        }

        unset($this->questions);
        $this->closeQuestionModal();
        session()->flash('success', __('Frage gespeichert.'));
    }

    public function deleteQuestion(string $questionId): void
    {
        Question::where('id', $questionId)
            ->where('assessment_id', $this->assessment->id)
            ->delete();
        unset($this->questions);
        session()->flash('success', __('Frage gelöscht.'));
    }

    public function duplicateQuestion(string $questionId): void
    {
        $original = Question::with('options')->find($questionId);
        if ($original && $original->assessment_id === $this->assessment->id) {
            $maxPosition = $this->assessment->questions()->max('position') ?? 0;

            $newQuestion = $original->replicate();
            $newQuestion->position = $maxPosition + 1;
            $newQuestion->save();

            foreach ($original->options as $option) {
                $newOption = $option->replicate();
                $newOption->question_id = $newQuestion->id;
                $newOption->save();
            }

            unset($this->questions);
            session()->flash('success', __('Frage dupliziert.'));
        }
    }

    public function updateQuestionOrder(array $order): void
    {
        foreach ($order as $position => $questionId) {
            Question::where('id', $questionId)
                ->where('assessment_id', $this->assessment->id)
                ->update(['position' => $position + 1]);
        }
        unset($this->questions);
    }

    // Quick add for true/false
    public function addTrueFalseQuestion(): void
    {
        $this->questionType = 'true_false';
        $this->questionOptions = [
            ['id' => null, 'text' => __('Richtig'), 'is_correct' => true, 'feedback' => ''],
            ['id' => null, 'text' => __('Falsch'), 'is_correct' => false, 'feedback' => ''],
        ];
        $this->showQuestionModal = true;
    }

    public function render()
    {
        return view('livewire.instructor.assessments.assessment-builder')
            ->layout('layouts.instructor', ['title' => __('Prüfung erstellen') . ' - ' . $this->assessment->title]);
    }
}
