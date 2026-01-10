<?php

namespace App\Livewire\Instructor\LearningPaths;

use App\Enums\StepType;
use App\Models\LearningPath;
use App\Models\LearningStep;
use App\Models\Module;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    public LearningPath $path;

    // Module modal state
    public bool $showModuleModal = false;

    public ?string $editingModuleId = null;

    public string $moduleTitle = '';

    public string $moduleDescription = '';

    // Step modal state
    public bool $showStepModal = false;

    public ?string $selectedModuleId = null;

    public ?string $editingStepId = null;

    public string $stepTitle = '';

    public string $stepDescription = '';

    public string $stepType = 'material';

    public int $stepPoints = 10;

    public int $stepMinutes = 5;

    // Delete confirmation
    public bool $showDeleteModal = false;

    public ?string $deleteType = null;

    public ?string $deleteId = null;

    public function mount(LearningPath $path): void
    {
        if ($path->creator_id !== Auth::id()) {
            abort(403);
        }

        $this->path = $path->load(['modules.steps', 'category', 'tags']);
    }

    #[Computed]
    public function modules()
    {
        return $this->path->modules()->with('steps')->ordered()->get();
    }

    // Module actions
    public function openModuleModal(?string $moduleId = null): void
    {
        if ($moduleId) {
            $module = Module::find($moduleId);
            $this->editingModuleId = $moduleId;
            $this->moduleTitle = $module->title;
            $this->moduleDescription = $module->description ?? '';
        } else {
            $this->editingModuleId = null;
            $this->moduleTitle = '';
            $this->moduleDescription = '';
        }
        $this->showModuleModal = true;
    }

    public function closeModuleModal(): void
    {
        $this->showModuleModal = false;
        $this->editingModuleId = null;
        $this->moduleTitle = '';
        $this->moduleDescription = '';
    }

    public function saveModule(): void
    {
        $this->validate([
            'moduleTitle' => ['required', 'string', 'min:2', 'max:255'],
            'moduleDescription' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($this->editingModuleId) {
            $module = Module::find($this->editingModuleId);
            $module->update([
                'title' => $this->moduleTitle,
                'description' => $this->moduleDescription,
            ]);
        } else {
            $maxPosition = $this->path->modules()->max('position') ?? 0;
            Module::create([
                'learning_path_id' => $this->path->id,
                'title' => $this->moduleTitle,
                'description' => $this->moduleDescription,
                'position' => $maxPosition + 1,
            ]);
        }

        unset($this->modules);
        $this->closeModuleModal();
    }

    public function updateModuleOrder(array $order): void
    {
        foreach ($order as $position => $moduleId) {
            Module::where('id', $moduleId)->update(['position' => $position + 1]);
        }
        unset($this->modules);
    }

    // Step actions
    public function openStepModal(string $moduleId, ?string $stepId = null): void
    {
        $this->selectedModuleId = $moduleId;

        if ($stepId) {
            $step = LearningStep::find($stepId);
            $this->editingStepId = $stepId;
            $this->stepTitle = $step->title;
            $this->stepDescription = $step->description ?? '';
            $this->stepType = $step->step_type->value;
            $this->stepPoints = $step->points_value ?? 10;
            $this->stepMinutes = $step->estimated_minutes ?? 5;
        } else {
            $this->editingStepId = null;
            $this->stepTitle = '';
            $this->stepDescription = '';
            $this->stepType = 'material';
            $this->stepPoints = 10;
            $this->stepMinutes = 5;
        }
        $this->showStepModal = true;
    }

    public function closeStepModal(): void
    {
        $this->showStepModal = false;
        $this->selectedModuleId = null;
        $this->editingStepId = null;
        $this->stepTitle = '';
        $this->stepDescription = '';
        $this->stepType = 'material';
        $this->stepPoints = 10;
        $this->stepMinutes = 5;
    }

    public function saveStep(): void
    {
        $this->validate([
            'stepTitle' => ['required', 'string', 'min:2', 'max:255'],
            'stepDescription' => ['nullable', 'string', 'max:1000'],
            'stepType' => ['required', 'in:material,task,assessment'],
            'stepPoints' => ['required', 'integer', 'min:0', 'max:1000'],
            'stepMinutes' => ['required', 'integer', 'min:1', 'max:600'],
        ]);

        if ($this->editingStepId) {
            $step = LearningStep::find($this->editingStepId);
            $step->update([
                'title' => $this->stepTitle,
                'description' => $this->stepDescription,
                'step_type' => StepType::from($this->stepType),
                'points_value' => $this->stepPoints,
                'estimated_minutes' => $this->stepMinutes,
            ]);
        } else {
            $maxPosition = LearningStep::where('module_id', $this->selectedModuleId)->max('position') ?? 0;
            $step = LearningStep::create([
                'module_id' => $this->selectedModuleId,
                'title' => $this->stepTitle,
                'description' => $this->stepDescription,
                'step_type' => StepType::from($this->stepType),
                'points_value' => $this->stepPoints,
                'estimated_minutes' => $this->stepMinutes,
                'position' => $maxPosition + 1,
            ]);
        }

        unset($this->modules);
        $this->closeStepModal();

        // Redirect to step editor for new steps
        if (! $this->editingStepId) {
            $this->redirect(route('instructor.steps.edit', $step), navigate: true);
        }
    }

    public function updateStepOrder(string $moduleId, array $order): void
    {
        foreach ($order as $position => $stepId) {
            LearningStep::where('id', $stepId)->update(['position' => $position + 1]);
        }
        unset($this->modules);
    }

    // Delete actions
    public function confirmDelete(string $type, string $id): void
    {
        $this->deleteType = $type;
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deleteType = null;
        $this->deleteId = null;
    }

    public function delete(): void
    {
        if ($this->deleteType === 'module') {
            Module::where('id', $this->deleteId)->delete();
        } elseif ($this->deleteType === 'step') {
            LearningStep::where('id', $this->deleteId)->delete();
        }

        unset($this->modules);
        $this->cancelDelete();
    }

    // Publishing
    public function togglePublish(): void
    {
        $this->path->update(['is_published' => ! $this->path->is_published]);
        $this->path->refresh();
    }

    public function render()
    {
        return view('livewire.instructor.learning-paths.show')
            ->layout('layouts.instructor', ['title' => $this->path->title]);
    }
}
