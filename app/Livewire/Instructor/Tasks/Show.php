<?php

namespace App\Livewire\Instructor\Tasks;

use App\Models\Task;
use App\Models\TaskSubmission;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Show extends Component
{
    public Task $task;

    public function mount(Task $task): void
    {
        $this->task = $task->load('step.module.learningPath');

        // Verify ownership
        if ($this->task->step->module->learningPath->creator_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403);
        }
    }

    #[Computed]
    public function submissions()
    {
        return TaskSubmission::where('task_id', $this->task->id)
            ->with(['enrollment.user'])
            ->orderByDesc('submitted_at')
            ->paginate(20);
    }

    #[Computed]
    public function stats(): array
    {
        $total = TaskSubmission::where('task_id', $this->task->id)->count();
        $pending = TaskSubmission::where('task_id', $this->task->id)->where('status', 'pending')->count();
        $reviewed = TaskSubmission::where('task_id', $this->task->id)->where('status', 'reviewed')->count();
        $avgScore = TaskSubmission::where('task_id', $this->task->id)
            ->whereNotNull('score')
            ->avg('score');

        return [
            'total' => $total,
            'pending' => $pending,
            'reviewed' => $reviewed,
            'avg_score' => $avgScore ? round($avgScore, 1) : null,
            'pass_rate' => $reviewed > 0
                ? round((TaskSubmission::where('task_id', $this->task->id)
                    ->whereNotNull('score')
                    ->whereRaw('score >= (? * 0.6)', [$this->task->max_points])
                    ->count() / $reviewed) * 100, 1)
                : 0,
        ];
    }

    public function reviewSubmission(string $submissionId): void
    {
        $this->redirect(route('instructor.submissions.review', $submissionId), navigate: true);
    }

    public function editTask(): void
    {
        $this->redirect(route('instructor.tasks.edit', $this->task->id), navigate: true);
    }

    public function render()
    {
        return view('livewire.instructor.tasks.show')
            ->layout('layouts.instructor', ['title' => $this->task->title]);
    }
}
