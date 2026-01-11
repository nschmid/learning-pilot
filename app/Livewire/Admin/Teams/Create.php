<?php

namespace App\Livewire\Admin\Teams;

use App\Models\Team;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|exists:users,id')]
    public ?string $ownerId = null;

    #[Validate('nullable|string|max:1000')]
    public string $description = '';

    public string $ownerSearch = '';

    #[Computed]
    public function availableOwners()
    {
        $query = User::query()
            ->orderBy('name')
            ->limit(20);

        if ($this->ownerSearch) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->ownerSearch}%")
                    ->orWhere('email', 'like', "%{$this->ownerSearch}%");
            });
        }

        return $query->get(['id', 'name', 'email']);
    }

    public function save(): void
    {
        $this->validate();

        $owner = User::findOrFail($this->ownerId);

        $team = Team::create([
            'name' => $this->name,
            'user_id' => $owner->id,
            'personal_team' => false,
        ]);

        // Add owner to team
        $team->users()->attach($owner, ['role' => 'admin']);

        // Update user's current team if not set
        if (! $owner->current_team_id) {
            $owner->update(['current_team_id' => $team->id]);
        }

        session()->flash('success', __('Team wurde erfolgreich erstellt.'));

        $this->redirect(route('admin.teams.show', $team), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.teams.create')
            ->layout('layouts.admin', ['title' => __('Team erstellen')]);
    }
}
