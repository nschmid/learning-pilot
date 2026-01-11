<?php

namespace App\Livewire\Instructor\Students;

use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Show extends Component
{
    public Enrollment $enrollment;

    public function mount(Enrollment $enrollment): void
    {
        $this->enrollment = $enrollment->load([
            'user',
            'learningPath.modules.steps',
            'stepProgress',
            'taskSubmissions.task',
            'assessmentAttempts.assessment',
        ]);

        // Verify ownership
        if ($this->enrollment->learningPath->creator_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403);
        }
    }

    #[Computed]
    public function moduleProgress(): array
    {
        $modules = [];

        foreach ($this->enrollment->learningPath->modules as $module) {
            $totalSteps = $module->steps->count();
            $completedSteps = $this->enrollment->stepProgress
                ->whereIn('step_id', $module->steps->pluck('id'))
                ->where('status', 'completed')
                ->count();

            $modules[] = [
                'id' => $module->id,
                'title' => $module->title,
                'total_steps' => $totalSteps,
                'completed_steps' => $completedSteps,
                'progress' => $totalSteps > 0 ? round(($completedSteps / $totalSteps) * 100) : 0,
            ];
        }

        return $modules;
    }

    #[Computed]
    public function recentSubmissions(): array
    {
        return $this->enrollment->taskSubmissions()
            ->with('task.step')
            ->orderByDesc('submitted_at')
            ->limit(5)
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'task_title' => $s->task->title,
                'step_title' => $s->task->step->title,
                'status' => $s->status->value,
                'score' => $s->score,
                'max_points' => $s->task->max_points,
                'submitted_at' => $s->submitted_at->format('d.m.Y H:i'),
            ])
            ->toArray();
    }

    #[Computed]
    public function assessmentResults(): array
    {
        return $this->enrollment->assessmentAttempts()
            ->with('assessment.step')
            ->orderByDesc('started_at')
            ->limit(5)
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'assessment_title' => $a->assessment->title,
                'step_title' => $a->assessment->step->title,
                'score_percent' => $a->score_percent,
                'passed' => $a->passed,
                'started_at' => $a->started_at->format('d.m.Y H:i'),
            ])
            ->toArray();
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'total_time' => $this->enrollment->getFormattedTimeSpent(),
            'points_earned' => $this->enrollment->points_earned,
            'tasks_submitted' => $this->enrollment->taskSubmissions->count(),
            'tasks_pending' => $this->enrollment->taskSubmissions->where('status.value', 'pending')->count(),
            'assessments_taken' => $this->enrollment->assessmentAttempts->count(),
            'assessments_passed' => $this->enrollment->assessmentAttempts->where('passed', true)->count(),
        ];
    }

    public function viewSubmission(string $submissionId): void
    {
        $this->redirect(route('instructor.submissions.review', $submissionId), navigate: true);
    }

    public function render()
    {
        return view('livewire.instructor.students.show')
            ->layout('layouts.instructor', ['title' => $this->enrollment->user->name]);
    }
}
