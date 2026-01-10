<?php

namespace App\Livewire\Admin\Teams;

use App\Models\Enrollment;
use App\Models\Team;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Show extends Component
{
    public Team $team;

    public function mount(Team $team): void
    {
        $this->team = $team->load(['owner', 'users']);
    }

    #[Computed]
    public function stats(): array
    {
        $memberIds = $this->team->allUsers()->pluck('id');
        $enrollments = Enrollment::whereIn('user_id', $memberIds);

        return [
            'total_members' => $memberIds->count(),
            'total_enrollments' => $enrollments->count(),
            'active_enrollments' => (clone $enrollments)->where('status', 'active')->count(),
            'completed_enrollments' => (clone $enrollments)->where('status', 'completed')->count(),
            'avg_progress' => round((clone $enrollments)->avg('progress_percent') ?? 0),
            'total_time_hours' => round((clone $enrollments)->sum('total_time_spent_seconds') / 3600, 1),
        ];
    }

    #[Computed]
    public function members(): Collection
    {
        return $this->team->allUsers()->sortBy('name');
    }

    #[Computed]
    public function recentEnrollments(): Collection
    {
        $memberIds = $this->team->allUsers()->pluck('id');

        return Enrollment::with(['user', 'learningPath'])
            ->whereIn('user_id', $memberIds)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.teams.show')
            ->layout('layouts.admin', ['title' => $this->team->name]);
    }
}
