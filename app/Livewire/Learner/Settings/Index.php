<?php

namespace App\Livewire\Learner\Settings;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class Index extends Component
{
    // Profile fields
    public string $name = '';
    public string $email = '';
    public string $bio = '';

    // Password fields
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Notification preferences
    public bool $email_progress = true;
    public bool $email_feedback = true;
    public bool $email_certificates = true;

    // Learning preferences
    public string $theme = 'light';
    public bool $autoplay = true;

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->bio = $user->bio ?? '';

        // Load user preferences if they exist
        $preferences = $user->preferences ?? [];
        $this->email_progress = $preferences['email_progress'] ?? true;
        $this->email_feedback = $preferences['email_feedback'] ?? true;
        $this->email_certificates = $preferences['email_certificates'] ?? true;
        $this->theme = $preferences['theme'] ?? 'light';
        $this->autoplay = $preferences['autoplay'] ?? true;
    }

    public function updateProfile(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . Auth::id()],
            'bio' => ['nullable', 'string', 'max:1000'],
        ]);

        Auth::user()->update([
            'name' => $this->name,
            'email' => $this->email,
            'bio' => $this->bio,
        ]);

        $this->dispatch('saved');
        session()->flash('profile_status', __('Profil erfolgreich aktualisiert.'));
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        Auth::user()->update([
            'password' => Hash::make($this->password),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);
        $this->dispatch('saved');
        session()->flash('password_status', __('Passwort erfolgreich geändert.'));
    }

    public function updateNotifications(): void
    {
        $user = Auth::user();
        $preferences = $user->preferences ?? [];

        $preferences['email_progress'] = $this->email_progress;
        $preferences['email_feedback'] = $this->email_feedback;
        $preferences['email_certificates'] = $this->email_certificates;

        $user->update(['preferences' => $preferences]);

        $this->dispatch('saved');
        session()->flash('notification_status', __('Benachrichtigungseinstellungen gespeichert.'));
    }

    public function updateLearningPreferences(): void
    {
        $user = Auth::user();
        $preferences = $user->preferences ?? [];

        $preferences['theme'] = $this->theme;
        $preferences['autoplay'] = $this->autoplay;

        $user->update(['preferences' => $preferences]);

        $this->dispatch('saved');
        session()->flash('learning_status', __('Lerneinstellungen gespeichert.'));
    }

    public function render()
    {
        return view('livewire.learner.settings.index')
            ->layout('layouts.learner', ['title' => __('Einstellungen')]);
    }
}
