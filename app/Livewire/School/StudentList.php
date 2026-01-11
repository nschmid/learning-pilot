<?php

namespace App\Livewire\School;

use App\Actions\School\InviteStudentAction;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Lernende verwalten - LearningPilot')]
class StudentList extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $role = '';

    public bool $showInviteModal = false;
    public string $inviteName = '';
    public string $inviteEmail = '';
    public string $inviteRole = 'learner';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function removeUser(string $userId): void
    {
        $team = auth()->user()->currentTeam;
        $user = User::find($userId);

        if ($user && $team->hasUser($user) && $user->id !== $team->user_id) {
            $team->users()->detach($user);
            session()->flash('success', __('Benutzer wurde entfernt.'));
        }
    }

    public function openInviteModal(): void
    {
        $this->showInviteModal = true;
        $this->reset(['inviteName', 'inviteEmail', 'inviteRole']);
    }

    public function invite(): void
    {
        $this->validate([
            'inviteName' => 'required|string|min:2|max:100',
            'inviteEmail' => 'required|email',
            'inviteRole' => 'required|in:learner,instructor',
        ]);

        $team = auth()->user()->currentTeam;
        $action = app(InviteStudentAction::class);

        $result = $action->execute($team, [
            'name' => $this->inviteName,
            'email' => $this->inviteEmail,
            'role' => $this->inviteRole,
        ]);

        if ($result->success) {
            session()->flash('success', $result->message);
            $this->showInviteModal = false;
        } else {
            session()->flash('error', $result->message);
        }
    }

    public function render()
    {
        $team = auth()->user()->currentTeam;

        $query = $team->users()
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->role, function ($q) {
                $q->whereHas('roles', fn ($q) => $q->where('name', $this->role));
            })
            ->orderBy('name');

        return view('livewire.school.student-list', [
            'users' => $query->paginate(20),
            'totalStudents' => $team->users()->whereHas('roles', fn ($q) => $q->where('name', 'learner'))->count(),
            'totalInstructors' => $team->users()->whereHas('roles', fn ($q) => $q->where('name', 'instructor'))->count(),
        ]);
    }
}
