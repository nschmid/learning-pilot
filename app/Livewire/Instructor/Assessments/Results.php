<?php

namespace App\Livewire\Instructor\Assessments;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Results extends Component
{
    use WithPagination;

    public Assessment $assessment;

    public function mount(Assessment $assessment): void
    {
        $this->assessment = $assessment->load('step.module.learningPath', 'questions');

        // Verify ownership
        if ($this->assessment->step->module->learningPath->creator_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403);
        }
    }

    #[Computed]
    public function attempts()
    {
        return AssessmentAttempt::where('assessment_id', $this->assessment->id)
            ->whereNotNull('completed_at')
            ->with('enrollment.user')
            ->orderByDesc('completed_at')
            ->paginate(20);
    }

    #[Computed]
    public function stats(): array
    {
        $attempts = AssessmentAttempt::where('assessment_id', $this->assessment->id)
            ->whereNotNull('completed_at');

        $total = $attempts->count();
        $passed = $attempts->clone()->where('passed', true)->count();
        $avgScore = $attempts->clone()->avg('score_percent');
        $avgTime = $attempts->clone()->avg('time_spent_seconds');

        return [
            'total_attempts' => $total,
            'passed' => $passed,
            'failed' => $total - $passed,
            'pass_rate' => $total > 0 ? round(($passed / $total) * 100, 1) : 0,
            'avg_score' => $avgScore ? round($avgScore, 1) : 0,
            'avg_time' => $avgTime ? $this->formatTime((int) $avgTime) : '-',
        ];
    }

    #[Computed]
    public function questionStats(): array
    {
        $questions = $this->assessment->questions;
        $stats = [];

        foreach ($questions as $question) {
            $responses = $question->responses()->count();
            $correct = $question->responses()->where('is_correct', true)->count();

            $stats[] = [
                'id' => $question->id,
                'question_text' => \Illuminate\Support\Str::limit($question->question_text, 80),
                'total_responses' => $responses,
                'correct' => $correct,
                'success_rate' => $responses > 0 ? round(($correct / $responses) * 100, 1) : 0,
            ];
        }

        return $stats;
    }

    private function formatTime(int $seconds): string
    {
        $minutes = floor($seconds / 60);
        $secs = $seconds % 60;

        return sprintf('%d:%02d', $minutes, $secs);
    }

    public function render()
    {
        return view('livewire.instructor.assessments.results')
            ->layout('layouts.instructor', ['title' => __('Testergebnisse') . ' - ' . $this->assessment->title]);
    }
}
