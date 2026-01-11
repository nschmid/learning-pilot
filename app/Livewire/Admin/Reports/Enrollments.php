<?php

namespace App\Livewire\Admin\Reports;

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Enrollments extends Component
{
    use WithPagination;

    #[Url]
    public string $period = 'month';

    #[Url]
    public string $status = '';

    public function updatedPeriod(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function dateRange(): array
    {
        return match ($this->period) {
            'week' => [now()->subWeek(), now()],
            'month' => [now()->subMonth(), now()],
            'quarter' => [now()->subQuarter(), now()],
            'year' => [now()->subYear(), now()],
            'all' => [null, null],
            default => [now()->subMonth(), now()],
        };
    }

    #[Computed]
    public function stats(): array
    {
        [$start, $end] = $this->dateRange;

        $query = Enrollment::query();
        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }

        return [
            'total' => $query->clone()->count(),
            'active' => $query->clone()->where('status', EnrollmentStatus::Active)->count(),
            'completed' => $query->clone()->where('status', EnrollmentStatus::Completed)->count(),
            'paused' => $query->clone()->where('status', EnrollmentStatus::Paused)->count(),
            'avg_progress' => round($query->clone()->avg('progress_percent') ?? 0, 1),
            'avg_time' => $this->formatTime((int) ($query->clone()->avg('total_time_spent_seconds') ?? 0)),
        ];
    }

    #[Computed]
    public function enrollmentTrend(): array
    {
        [$start, $end] = $this->dateRange;

        $query = Enrollment::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->groupBy('date')
            ->orderBy('date');

        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }

        return $query->get()
            ->map(fn ($row) => [
                'date' => $row->date,
                'count' => $row->count,
            ])
            ->toArray();
    }

    #[Computed]
    public function topPathsByEnrollments(): array
    {
        [$start, $end] = $this->dateRange;

        $query = Enrollment::select('learning_path_id', DB::raw('COUNT(*) as count'))
            ->with('learningPath')
            ->groupBy('learning_path_id')
            ->orderByDesc('count')
            ->limit(10);

        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }

        return $query->get()
            ->map(fn ($row) => [
                'path_title' => $row->learningPath?->title ?? '-',
                'count' => $row->count,
            ])
            ->toArray();
    }

    #[Computed]
    public function recentEnrollments()
    {
        [$start, $end] = $this->dateRange;

        $query = Enrollment::with(['user', 'learningPath']);

        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->orderByDesc('created_at')
            ->paginate(20);
    }

    private function formatTime(int $seconds): string
    {
        if ($seconds === 0) {
            return '-';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$minutes}m";
    }

    public function render()
    {
        return view('livewire.admin.reports.enrollments')
            ->layout('layouts.admin', ['title' => __('Einschreibungs-Bericht')]);
    }
}
