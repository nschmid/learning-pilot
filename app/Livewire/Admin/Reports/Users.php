<?php

namespace App\Livewire\Admin\Reports;

use App\Enums\UserRole;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $role = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $period = 'all';

    #[Url]
    public string $sortBy = 'created_at';

    #[Url]
    public string $sortDir = 'desc';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRole(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedPeriod(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'desc';
        }
    }

    #[Computed]
    public function dateRange(): array
    {
        return match ($this->period) {
            'week' => [now()->subWeek(), now()],
            'month' => [now()->subMonth(), now()],
            'quarter' => [now()->subQuarter(), now()],
            'year' => [now()->subYear(), now()],
            default => [null, null],
        };
    }

    #[Computed]
    public function stats(): array
    {
        [$start, $end] = $this->dateRange;

        $query = User::query();
        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }

        return [
            'total' => $query->clone()->count(),
            'active' => $query->clone()->where('is_active', true)->count(),
            'inactive' => $query->clone()->where('is_active', false)->count(),
            'learners' => $query->clone()->where('role', UserRole::Learner)->count(),
            'instructors' => $query->clone()->where('role', UserRole::Instructor)->count(),
            'admins' => $query->clone()->where('role', UserRole::Admin)->count(),
        ];
    }

    #[Computed]
    public function registrationTrend(): array
    {
        $days = match ($this->period) {
            'week' => 7,
            'month' => 30,
            'quarter' => 90,
            'year' => 365,
            default => 30,
        };

        return User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'date' => $row->date,
                'count' => $row->count,
            ])
            ->toArray();
    }

    #[Computed]
    public function topLearners(): array
    {
        return User::where('role', UserRole::Learner)
            ->withCount(['enrollments', 'enrollments as completed_count' => function ($q) {
                $q->where('status', 'completed');
            }])
            ->withSum('enrollments', 'points_earned')
            ->orderByDesc('enrollments_sum_points_earned')
            ->limit(5)
            ->get()
            ->map(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'enrollments' => $user->enrollments_count,
                'completed' => $user->completed_count,
                'points' => $user->enrollments_sum_points_earned ?? 0,
            ])
            ->toArray();
    }

    #[Computed]
    public function topInstructors(): array
    {
        return User::where('role', UserRole::Instructor)
            ->withCount(['createdLearningPaths', 'createdLearningPaths as published_count' => function ($q) {
                $q->where('is_published', true);
            }])
            ->orderByDesc('created_learning_paths_count')
            ->limit(5)
            ->get()
            ->map(function ($user) {
                $totalEnrollments = Enrollment::whereHas('learningPath', function ($q) use ($user) {
                    $q->where('creator_id', $user->id);
                })->count();

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'paths' => $user->created_learning_paths_count,
                    'published' => $user->published_count,
                    'enrollments' => $totalEnrollments,
                ];
            })
            ->toArray();
    }

    #[Computed]
    public function users()
    {
        [$start, $end] = $this->dateRange;

        return User::query()
            ->when($start && $end, fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->role, fn ($q) => $q->where('role', $this->role))
            ->when($this->status !== '', function ($q) {
                $q->where('is_active', $this->status === 'active');
            })
            ->withCount('enrollments')
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(20);
    }

    public function render()
    {
        return view('livewire.admin.reports.users')
            ->layout('layouts.admin', ['title' => __('Benutzer-Bericht')]);
    }
}
