<?php

namespace App\Livewire\Instructor\Modules;

use App\Enums\StepType;
use App\Enums\UnlockCondition;
use App\Models\LearningPath;
use App\Models\LearningStep;
use App\Models\Module;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ModuleManager extends Component
{
    public LearningPath $path;

    // Module form
    public bool $showModuleModal = false;
    public ?string $editingModuleId = null;
    public string $moduleTitle = '';
    public string $moduleDescription = '';
    public string $unlockCondition = 'sequential';
    public ?int $unlockValue = null;
    public bool $moduleIsRequired = true;

    // Step form
    public bool $showStepModal = false;
    public ?string $editingStepId = null;
    public ?string $currentModuleId = null;
    public string $stepTitle = '';
    public string $stepDescription = '';
    public string $stepType = 'material';
    public int $stepPointsValue = 10;
    public ?int $stepEstimatedMinutes = null;
    public bool $stepIsRequired = true;
    public bool $stepIsPreview = false;

    public function mount(LearningPath $path): void
    {
        if ($path->creator_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $this->path = $path;
    }

    #[Computed]
    public function modules()
    {
        return $this->path->modules()
            ->with(['steps' => fn($q) => $q->orderBy('position')])
            ->orderBy('position')
            ->get();
    }

    // Module methods
    public function openModuleModal(?string $moduleId = null): void
    {
        if ($moduleId) {
            $module = Module::find($moduleId);
            $this->editingModuleId = $moduleId;
            $this->moduleTitle = $module->title;
            $this->moduleDescription = $module->description ?? '';
            $this->unlockCondition = $module->unlock_condition?->value ?? 'sequential';
            $this->unlockValue = $module->unlock_value;
            $this->moduleIsRequired = $module->is_required;
        } else {
            $this->resetModuleForm();
        }
        $this->showModuleModal = true;
    }

    public function closeModuleModal(): void
    {
        $this->showModuleModal = false;
        $this->resetModuleForm();
    }

    protected function resetModuleForm(): void
    {
        $this->editingModuleId = null;
        $this->moduleTitle = '';
        $this->moduleDescription = '';
        $this->unlockCondition = 'sequential';
        $this->unlockValue = null;
        $this->moduleIsRequired = true;
    }

    public function saveModule(): void
    {
        $this->validate([
            'moduleTitle' => ['required', 'string', 'min:2', 'max:255'],
            'moduleDescription' => ['nullable', 'string', 'max:2000'],
            'unlockCondition' => ['required', 'in:sequential,completion_percent,manual'],
            'unlockValue' => $this->unlockCondition === 'completion_percent' ? ['required', 'integer', 'min:1', 'max:100'] : ['nullable'],
        ]);

        $data = [
            'learning_path_id' => $this->path->id,
            'title' => $this->moduleTitle,
            'description' => $this->moduleDescription ?: null,
            'unlock_condition' => UnlockCondition::from($this->unlockCondition),
            'unlock_value' => $this->unlockValue,
            'is_required' => $this->moduleIsRequired,
        ];

        if ($this->editingModuleId) {
            Module::where('id', $this->editingModuleId)->update($data);
        } else {
            $maxPosition = $this->path->modules()->max('position') ?? 0;
            $data['position'] = $maxPosition + 1;
            Module::create($data);
        }

        unset($this->modules);
        $this->closeModuleModal();
        session()->flash('success', __('Modul gespeichert.'));
    }

    public function deleteModule(string $moduleId): void
    {
        $module = Module::find($moduleId);
        if ($module && $module->learning_path_id === $this->path->id) {
            $module->delete();
            unset($this->modules);
            session()->flash('success', __('Modul gelöscht.'));
        }
    }

    public function updateModuleOrder(array $order): void
    {
        foreach ($order as $position => $moduleId) {
            Module::where('id', $moduleId)
                ->where('learning_path_id', $this->path->id)
                ->update(['position' => $position + 1]);
        }
        unset($this->modules);
    }

    // Step methods
    public function openStepModal(string $moduleId, ?string $stepId = null): void
    {
        $this->currentModuleId = $moduleId;

        if ($stepId) {
            $step = LearningStep::find($stepId);
            $this->editingStepId = $stepId;
            $this->stepTitle = $step->title;
            $this->stepDescription = $step->description ?? '';
            $this->stepType = $step->step_type->value;
            $this->stepPointsValue = $step->points_value;
            $this->stepEstimatedMinutes = $step->estimated_minutes;
            $this->stepIsRequired = $step->is_required;
            $this->stepIsPreview = $step->is_preview;
        } else {
            $this->resetStepForm();
        }
        $this->showStepModal = true;
    }

    public function closeStepModal(): void
    {
        $this->showStepModal = false;
        $this->resetStepForm();
    }

    protected function resetStepForm(): void
    {
        $this->editingStepId = null;
        $this->currentModuleId = null;
        $this->stepTitle = '';
        $this->stepDescription = '';
        $this->stepType = 'material';
        $this->stepPointsValue = 10;
        $this->stepEstimatedMinutes = null;
        $this->stepIsRequired = true;
        $this->stepIsPreview = false;
    }

    public function saveStep(): void
    {
        $this->validate([
            'stepTitle' => ['required', 'string', 'min:2', 'max:255'],
            'stepDescription' => ['nullable', 'string', 'max:2000'],
            'stepType' => ['required', 'in:material,task,assessment'],
            'stepPointsValue' => ['required', 'integer', 'min:0', 'max:1000'],
            'stepEstimatedMinutes' => ['nullable', 'integer', 'min:1', 'max:600'],
        ]);

        $data = [
            'module_id' => $this->currentModuleId,
            'title' => $this->stepTitle,
            'description' => $this->stepDescription ?: null,
            'step_type' => StepType::from($this->stepType),
            'points_value' => $this->stepPointsValue,
            'estimated_minutes' => $this->stepEstimatedMinutes,
            'is_required' => $this->stepIsRequired,
            'is_preview' => $this->stepIsPreview,
        ];

        if ($this->editingStepId) {
            LearningStep::where('id', $this->editingStepId)->update($data);
            $stepId = $this->editingStepId;
        } else {
            $maxPosition = LearningStep::where('module_id', $this->currentModuleId)->max('position') ?? 0;
            $data['position'] = $maxPosition + 1;
            $step = LearningStep::create($data);
            $stepId = $step->id;
        }

        unset($this->modules);
        $this->closeStepModal();

        // Redirect to step editor for new steps
        if (!$this->editingStepId) {
            $this->redirect(route('instructor.steps.edit', $stepId), navigate: true);
        } else {
            session()->flash('success', __('Schritt gespeichert.'));
        }
    }

    public function deleteStep(string $stepId): void
    {
        $step = LearningStep::with('module')->find($stepId);
        if ($step && $step->module->learning_path_id === $this->path->id) {
            $step->delete();
            unset($this->modules);
            session()->flash('success', __('Schritt gelöscht.'));
        }
    }

    public function updateStepOrder(string $moduleId, array $order): void
    {
        foreach ($order as $position => $stepId) {
            LearningStep::where('id', $stepId)
                ->where('module_id', $moduleId)
                ->update(['position' => $position + 1]);
        }
        unset($this->modules);
    }

    public function editStep(string $stepId): void
    {
        $this->redirect(route('instructor.steps.edit', $stepId), navigate: true);
    }

    public function render()
    {
        return view('livewire.instructor.modules.module-manager')
            ->layout('layouts.instructor', ['title' => __('Module verwalten') . ' - ' . $this->path->title]);
    }
}
