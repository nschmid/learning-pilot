<?php

namespace App\Livewire\Instructor\Steps;

use App\Enums\MaterialType;
use App\Enums\QuestionType;
use App\Enums\StepType;
use App\Models\Assessment;
use App\Models\LearningMaterial;
use App\Models\LearningStep;
use App\Models\Question;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public LearningStep $step;

    // Material fields
    public string $materialType = 'text';

    public string $materialTitle = '';

    public string $materialContent = '';

    public $materialFile = null;

    public ?string $materialUrl = null;

    public bool $showMaterialModal = false;

    public ?string $editingMaterialId = null;

    // Task fields
    public string $taskTitle = '';

    public string $taskInstructions = '';

    public int $taskMaxPoints = 100;

    public ?int $taskDueDays = null;

    public bool $taskAllowResubmit = true;

    public ?int $taskMaxFileSizeMb = 10;

    // Assessment fields
    public string $assessmentTitle = '';

    public string $assessmentDescription = '';

    public ?int $assessmentTimeLimit = null;

    public int $assessmentPassingScore = 70;

    public int $assessmentMaxAttempts = 3;

    public bool $assessmentShuffleQuestions = true;

    // Question modal
    public bool $showQuestionModal = false;

    public ?string $editingQuestionId = null;

    public string $questionType = 'single_choice';

    public string $questionText = '';

    public string $questionExplanation = '';

    public int $questionPoints = 10;

    public array $questionOptions = [
        ['text' => '', 'is_correct' => false],
        ['text' => '', 'is_correct' => false],
    ];

    public function mount(LearningStep $step): void
    {
        if ($step->module->learningPath->creator_id !== Auth::id()) {
            abort(403);
        }

        $this->step = $step->load(['module.learningPath', 'materials', 'task', 'assessment.questions.options']);

        // Load existing task data
        if ($this->step->isTask() && $this->step->task) {
            $task = $this->step->task;
            $this->taskTitle = $task->title ?? '';
            $this->taskInstructions = $task->instructions ?? '';
            $this->taskMaxPoints = $task->max_points ?? 100;
            $this->taskDueDays = $task->due_days;
            $this->taskAllowResubmit = $task->allow_resubmit ?? true;
            $this->taskMaxFileSizeMb = $task->max_file_size_mb ?? 10;
        }

        // Load existing assessment data
        if ($this->step->isAssessment() && $this->step->assessment) {
            $assessment = $this->step->assessment;
            $this->assessmentTitle = $assessment->title ?? '';
            $this->assessmentDescription = $assessment->description ?? '';
            $this->assessmentTimeLimit = $assessment->time_limit_minutes;
            $this->assessmentPassingScore = $assessment->passing_score_percent ?? 70;
            $this->assessmentMaxAttempts = $assessment->max_attempts ?? 3;
            $this->assessmentShuffleQuestions = $assessment->shuffle_questions ?? true;
        }
    }

    #[Computed]
    public function materials()
    {
        return $this->step->materials()->ordered()->get();
    }

    #[Computed]
    public function questions()
    {
        return $this->step->assessment?->questions()->with('options')->ordered()->get() ?? collect();
    }

    // Material methods
    public function openMaterialModal(?string $materialId = null): void
    {
        if ($materialId) {
            $material = LearningMaterial::find($materialId);
            $this->editingMaterialId = $materialId;
            $this->materialType = $material->material_type->value;
            $this->materialTitle = $material->title;
            $this->materialContent = $material->content ?? '';
            $this->materialUrl = $material->file_path;
        } else {
            $this->resetMaterialForm();
        }
        $this->showMaterialModal = true;
    }

    public function closeMaterialModal(): void
    {
        $this->showMaterialModal = false;
        $this->resetMaterialForm();
    }

    protected function resetMaterialForm(): void
    {
        $this->editingMaterialId = null;
        $this->materialType = 'text';
        $this->materialTitle = '';
        $this->materialContent = '';
        $this->materialFile = null;
        $this->materialUrl = null;
    }

    public function saveMaterial(): void
    {
        $this->validate([
            'materialTitle' => ['required', 'string', 'min:2', 'max:255'],
            'materialType' => ['required', 'in:text,video,audio,pdf,image,link,interactive'],
            'materialContent' => $this->materialType === 'text' ? ['required', 'string'] : ['nullable'],
            'materialUrl' => in_array($this->materialType, ['video', 'link']) ? ['required', 'url'] : ['nullable'],
        ]);

        $data = [
            'step_id' => $this->step->id,
            'material_type' => MaterialType::from($this->materialType),
            'title' => $this->materialTitle,
            'content' => $this->materialType === 'text' ? $this->materialContent : null,
            'file_path' => in_array($this->materialType, ['video', 'link']) ? $this->materialUrl : null,
        ];

        if ($this->editingMaterialId) {
            LearningMaterial::where('id', $this->editingMaterialId)->update($data);
        } else {
            $maxPosition = $this->step->materials()->max('position') ?? 0;
            $data['position'] = $maxPosition + 1;
            LearningMaterial::create($data);
        }

        unset($this->materials);
        $this->closeMaterialModal();
    }

    public function deleteMaterial(string $materialId): void
    {
        LearningMaterial::where('id', $materialId)->delete();
        unset($this->materials);
    }

    public function updateMaterialOrder(array $order): void
    {
        foreach ($order as $position => $materialId) {
            LearningMaterial::where('id', $materialId)->update(['position' => $position + 1]);
        }
        unset($this->materials);
    }

    // Task methods
    public function saveTask(): void
    {
        $this->validate([
            'taskTitle' => ['required', 'string', 'min:2', 'max:255'],
            'taskInstructions' => ['required', 'string', 'min:10'],
            'taskMaxPoints' => ['required', 'integer', 'min:1', 'max:1000'],
            'taskDueDays' => ['nullable', 'integer', 'min:1'],
            'taskMaxFileSizeMb' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $data = [
            'step_id' => $this->step->id,
            'title' => $this->taskTitle,
            'instructions' => $this->taskInstructions,
            'max_points' => $this->taskMaxPoints,
            'due_days' => $this->taskDueDays,
            'allow_resubmit' => $this->taskAllowResubmit,
            'max_file_size_mb' => $this->taskMaxFileSizeMb,
        ];

        Task::updateOrCreate(
            ['step_id' => $this->step->id],
            $data
        );

        $this->step->refresh();
        session()->flash('success', __('Aufgabe gespeichert.'));
    }

    // Assessment methods
    public function saveAssessment(): void
    {
        $this->validate([
            'assessmentTitle' => ['required', 'string', 'min:2', 'max:255'],
            'assessmentDescription' => ['nullable', 'string', 'max:1000'],
            'assessmentTimeLimit' => ['nullable', 'integer', 'min:1', 'max:300'],
            'assessmentPassingScore' => ['required', 'integer', 'min:1', 'max:100'],
            'assessmentMaxAttempts' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        Assessment::updateOrCreate(
            ['step_id' => $this->step->id],
            [
                'title' => $this->assessmentTitle,
                'description' => $this->assessmentDescription,
                'time_limit_minutes' => $this->assessmentTimeLimit,
                'passing_score_percent' => $this->assessmentPassingScore,
                'max_attempts' => $this->assessmentMaxAttempts,
                'shuffle_questions' => $this->assessmentShuffleQuestions,
            ]
        );

        $this->step->refresh();
        session()->flash('success', __('Prüfung gespeichert.'));
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
            $this->questionOptions = $question->options->map(fn ($o) => [
                'text' => $o->option_text,
                'is_correct' => $o->is_correct,
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
            ['text' => '', 'is_correct' => false],
            ['text' => '', 'is_correct' => false],
        ];
    }

    public function addQuestionOption(): void
    {
        $this->questionOptions[] = ['text' => '', 'is_correct' => false];
    }

    public function removeQuestionOption(int $index): void
    {
        if (count($this->questionOptions) > 2) {
            unset($this->questionOptions[$index]);
            $this->questionOptions = array_values($this->questionOptions);
        }
    }

    public function saveQuestion(): void
    {
        $this->validate([
            'questionText' => ['required', 'string', 'min:5'],
            'questionType' => ['required', 'in:single_choice,multiple_choice,true_false,text'],
            'questionPoints' => ['required', 'integer', 'min:1', 'max:100'],
            'questionOptions' => $this->questionType !== 'text' ? ['required', 'array', 'min:2'] : ['nullable'],
            'questionOptions.*.text' => $this->questionType !== 'text' ? ['required', 'string'] : ['nullable'],
        ]);

        // Ensure assessment exists
        if (! $this->step->assessment) {
            $this->saveAssessment();
            $this->step->refresh();
        }

        $data = [
            'assessment_id' => $this->step->assessment->id,
            'question_type' => QuestionType::from($this->questionType),
            'question_text' => $this->questionText,
            'explanation' => $this->questionExplanation ?: null,
            'points' => $this->questionPoints,
        ];

        if ($this->editingQuestionId) {
            $question = Question::find($this->editingQuestionId);
            $question->update($data);
            $question->options()->delete();
        } else {
            $maxPosition = $this->step->assessment->questions()->max('position') ?? 0;
            $data['position'] = $maxPosition + 1;
            $question = Question::create($data);
        }

        // Save options (except for text questions)
        if ($this->questionType !== 'text') {
            foreach ($this->questionOptions as $index => $option) {
                if (! empty($option['text'])) {
                    $question->options()->create([
                        'option_text' => $option['text'],
                        'is_correct' => $option['is_correct'] ?? false,
                        'position' => $index + 1,
                    ]);
                }
            }
        }

        unset($this->questions);
        $this->closeQuestionModal();
    }

    public function deleteQuestion(string $questionId): void
    {
        Question::where('id', $questionId)->delete();
        unset($this->questions);
    }

    public function render()
    {
        return view('livewire.instructor.steps.edit')
            ->layout('layouts.instructor', ['title' => __('Schritt bearbeiten')]);
    }
}
