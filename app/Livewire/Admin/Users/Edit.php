<?php

namespace App\Livewire\Admin\Users;

use App\Enums\UserRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Edit extends Component
{
    public User $user;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $role = 'learner';

    public ?string $teamId = null;

    public bool $isActive = true;

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role->value;
        $this->teamId = $user->current_team_id;
        $this->isActive = $user->is_active;
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$this->user->id],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'in:admin,instructor,learner'],
            'teamId' => ['nullable', 'exists:teams,id'],
            'isActive' => ['boolean'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => __('Bitte gib einen Namen ein.'),
            'email.required' => __('Bitte gib eine E-Mail-Adresse ein.'),
            'email.email' => __('Bitte gib eine gültige E-Mail-Adresse ein.'),
            'email.unique' => __('Diese E-Mail-Adresse wird bereits verwendet.'),
            'password.min' => __('Das Passwort muss mindestens 8 Zeichen lang sein.'),
        ];
    }

    #[Computed]
    public function teams(): Collection
    {
        return Team::orderBy('name')->get();
    }

    #[Computed]
    public function roles(): array
    {
        return collect(UserRole::cases())
            ->map(fn ($role) => ['value' => $role->value, 'label' => $role->label()])
            ->all();
    }

    public function save(): void
    {
        $validated = $this->validate();

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => UserRole::from($validated['role']),
            'is_active' => $this->isActive,
            'current_team_id' => $validated['teamId'],
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $this->user->update($data);

        // Sync team membership if changed
        if ($validated['teamId'] && ! $this->user->belongsToTeam(Team::find($validated['teamId']))) {
            $team = Team::find($validated['teamId']);
            $this->user->teams()->attach($team, ['role' => 'member']);
        }

        session()->flash('success', __('Änderungen wurden gespeichert.'));
        $this->redirect(route('admin.users.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.users.edit')
            ->layout('layouts.admin', ['title' => __('Benutzer bearbeiten')]);
    }
}
