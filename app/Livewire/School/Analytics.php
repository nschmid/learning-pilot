<?php

namespace App\Livewire\School;

use App\Models\Enrollment;
use App\Models\LearningPath;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Statistiken - LearningPilot')]
class Analytics extends Component
{
    #[Url]
    public string $period = '30';

    public function render()
    {
        $team = auth()->user()->currentTeam;

        $startDate = now()->subDays((int) $this->period);

        // Get learning paths for the team
        $pathIds = $team->learningPaths()->pluck('id');

        // Enrollments over time
        $enrollmentsOverTime = Enrollment::whereIn('learning_path_id', $pathIds)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Completions over time
        $completionsOverTime = Enrollment::whereIn('learning_path_id', $pathIds)
            ->where('completed_at', '>=', $startDate)
            ->whereNotNull('completed_at')
            ->selectRaw('DATE(completed_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Top paths by enrollment
        $topPaths = LearningPath::where('team_id', $team->id)
            ->withCount(['enrollments' => fn ($q) => $q->where('created_at', '>=', $startDate)])
            ->orderByDesc('enrollments_count')
            ->limit(5)
            ->get();

        // Average completion rate
        $avgCompletionRate = Enrollment::whereIn('learning_path_id', $pathIds)
            ->avg('progress_percent') ?? 0;

        // Average time spent
        $avgTimeSpent = Enrollment::whereIn('learning_path_id', $pathIds)
            ->avg('total_time_spent_seconds') ?? 0;

        // Active learners today
        $activeToday = Enrollment::whereIn('learning_path_id', $pathIds)
            ->whereDate('last_activity_at', today())
            ->distinct('user_id')
            ->count('user_id');

        return view('livewire.school.analytics', [
            'enrollmentsOverTime' => $enrollmentsOverTime,
            'completionsOverTime' => $completionsOverTime,
            'topPaths' => $topPaths,
            'avgCompletionRate' => round($avgCompletionRate, 1),
            'avgTimeSpent' => $this->formatDuration($avgTimeSpent),
            'activeToday' => $activeToday,
            'periods' => [
                '7' => __('7 Tage'),
                '30' => __('30 Tage'),
                '90' => __('90 Tage'),
                '365' => __('1 Jahr'),
            ],
        ]);
    }

    protected function formatDuration(float $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($hours > 0) {
            return __(':hours Std :minutes Min', ['hours' => $hours, 'minutes' => $minutes]);
        }

        return __(':minutes Min', ['minutes' => $minutes]);
    }
}
