<?php

namespace App\Livewire\Instructor\Paths;

use App\Models\LearningPath;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Preview extends Component
{
    public LearningPath $path;
    public ?string $selectedModuleId = null;
    public ?string $selectedStepId = null;

    public function mount(LearningPath $path): void
    {
        $this->path = $path->load([
            'modules.steps.materials',
            'modules.steps.task',
            'modules.steps.assessment.questions',
            'category',
            'tags',
        ]);

        // Verify ownership
        if ($this->path->creator_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403);
        }

        // Select first module and step by default
        if ($this->path->modules->isNotEmpty()) {
            $firstModule = $this->path->modules->first();
            $this->selectedModuleId = $firstModule->id;

            if ($firstModule->steps->isNotEmpty()) {
                $this->selectedStepId = $firstModule->steps->first()->id;
            }
        }
    }

    #[Computed]
    public function selectedModule()
    {
        if (! $this->selectedModuleId) {
            return null;
        }

        return $this->path->modules->firstWhere('id', $this->selectedModuleId);
    }

    #[Computed]
    public function selectedStep()
    {
        if (! $this->selectedStepId || ! $this->selectedModule) {
            return null;
        }

        return $this->selectedModule->steps->firstWhere('id', $this->selectedStepId);
    }

    public function selectModule(string $moduleId): void
    {
        $this->selectedModuleId = $moduleId;
        $module = $this->path->modules->firstWhere('id', $moduleId);

        if ($module && $module->steps->isNotEmpty()) {
            $this->selectedStepId = $module->steps->first()->id;
        } else {
            $this->selectedStepId = null;
        }
    }

    public function selectStep(string $stepId): void
    {
        $this->selectedStepId = $stepId;
    }

    public function render()
    {
        return view('livewire.instructor.paths.preview')
            ->layout('layouts.instructor', ['title' => __('Vorschau') . ' - ' . $this->path->title]);
    }
}
