<?php

namespace App\Livewire\Admin\Users;

use App\Enums\UserRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Create extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $role = 'learner';

    public ?string $teamId = null;

    public bool $isActive = true;

    public bool $sendWelcomeEmail = true;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
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
            'password.required' => __('Bitte gib ein Passwort ein.'),
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

    public function generatePassword(): void
    {
        $this->password = Str::random(12);
    }

    public function save(): void
    {
        $validated = $this->validate();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => UserRole::from($validated['role']),
            'is_active' => $this->isActive,
            'current_team_id' => $validated['teamId'],
        ]);

        // Add to team if selected
        if ($validated['teamId']) {
            $team = Team::find($validated['teamId']);
            if ($team) {
                $user->teams()->attach($team, ['role' => 'member']);
            }
        }

        // TODO: Send welcome email if $this->sendWelcomeEmail is true

        session()->flash('success', __('Benutzer wurde erstellt.'));
        $this->redirect(route('admin.users.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.users.create')
            ->layout('layouts.admin', ['title' => __('Neuer Benutzer')]);
    }
}
