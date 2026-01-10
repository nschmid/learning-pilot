<?php

namespace App\Livewire\Admin\Users;

use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Show extends Component
{
    public User $user;

    public function mount(User $user): void
    {
        $this->user = $user->load(['currentTeam', 'teams']);
    }

    #[Computed]
    public function enrollments(): Collection
    {
        return Enrollment::with('learningPath')
            ->where('user_id', $this->user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }

    #[Computed]
    public function stats(): array
    {
        $enrollments = Enrollment::where('user_id', $this->user->id);

        return [
            'total_enrollments' => $enrollments->count(),
            'completed_enrollments' => (clone $enrollments)->where('status', 'completed')->count(),
            'total_points' => (clone $enrollments)->sum('points_earned'),
            'total_time' => (clone $enrollments)->sum('total_time_spent_seconds'),
        ];
    }

    public function toggleStatus(): void
    {
        $this->user->update(['is_active' => ! $this->user->is_active]);
        $this->user->refresh();
    }

    public function render()
    {
        return view('livewire.admin.users.show')
            ->layout('layouts.admin', ['title' => $this->user->name]);
    }
}
