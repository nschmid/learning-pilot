<?php

namespace App\Livewire\Instructor\Tasks;

use App\Enums\TaskType;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{
    public Task $task;

    public string $title = '';
    public string $task_type = '';
    public string $instructions = '';
    public int $max_points = 100;
    public ?int $due_days = null;
    public bool $allow_late = false;
    public bool $allow_resubmit = false;
    public array $rubric = [];
    public array $allowed_file_types = [];
    public ?int $max_file_size_mb = 10;

    public function mount(Task $task): void
    {
        $this->task = $task->load('step.module.learningPath');

        // Verify ownership
        if ($this->task->step->module->learningPath->creator_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403);
        }

        $this->title = $task->title;
        $this->task_type = $task->task_type->value;
        $this->instructions = $task->instructions ?? '';
        $this->max_points = $task->max_points;
        $this->due_days = $task->due_days;
        $this->allow_late = $task->allow_late ?? false;
        $this->allow_resubmit = $task->allow_resubmit ?? false;
        $this->rubric = $task->rubric ?? [];
        $this->allowed_file_types = $task->allowed_file_types ?? ['pdf', 'doc', 'docx'];
        $this->max_file_size_mb = $task->max_file_size_mb ?? 10;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'task_type' => ['required', Rule::enum(TaskType::class)],
            'instructions' => ['required', 'string'],
            'max_points' => ['required', 'integer', 'min:1', 'max:1000'],
            'due_days' => ['nullable', 'integer', 'min:1'],
            'allow_late' => ['boolean'],
            'allow_resubmit' => ['boolean'],
            'rubric' => ['array'],
            'rubric.*.name' => ['required_with:rubric', 'string', 'max:255'],
            'rubric.*.points' => ['required_with:rubric', 'integer', 'min:0'],
            'allowed_file_types' => ['array'],
            'max_file_size_mb' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function addRubricItem(): void
    {
        $this->rubric[] = ['name' => '', 'points' => 0];
    }

    public function removeRubricItem(int $index): void
    {
        unset($this->rubric[$index]);
        $this->rubric = array_values($this->rubric);
    }

    public function save(): void
    {
        $this->validate();

        $this->task->update([
            'title' => $this->title,
            'task_type' => $this->task_type,
            'instructions' => $this->instructions,
            'max_points' => $this->max_points,
            'due_days' => $this->due_days,
            'allow_late' => $this->allow_late,
            'allow_resubmit' => $this->allow_resubmit,
            'rubric' => $this->rubric,
            'allowed_file_types' => $this->allowed_file_types,
            'max_file_size_mb' => $this->max_file_size_mb,
        ]);

        session()->flash('success', __('Aufgabe wurde aktualisiert.'));
        $this->redirect(route('instructor.tasks.show', $this->task->id), navigate: true);
    }

    public function render()
    {
        return view('livewire.instructor.tasks.edit', [
            'taskTypes' => TaskType::cases(),
            'fileTypes' => ['pdf', 'doc', 'docx', 'txt', 'zip', 'jpg', 'png', 'mp4'],
        ])->layout('layouts.instructor', ['title' => __('Aufgabe bearbeiten')]);
    }
}
