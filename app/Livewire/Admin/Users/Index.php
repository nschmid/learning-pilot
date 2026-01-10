<?php

namespace App\Livewire\Admin\Users;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $role = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $sortBy = 'created_at';

    #[Url]
    public string $sortDir = 'desc';

    public bool $showDeleteModal = false;

    public ?string $userToDelete = null;

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

    public function sortBy(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
    }

    #[Computed]
    public function users(): LengthAwarePaginator
    {
        return User::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->role, function ($query) {
                $query->where('role', UserRole::from($this->role));
            })
            ->when($this->status !== '', function ($query) {
                $query->where('is_active', $this->status === 'active');
            })
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(15);
    }

    #[Computed]
    public function roles(): array
    {
        return collect(UserRole::cases())
            ->map(fn ($role) => ['value' => $role->value, 'label' => $role->label()])
            ->all();
    }

    public function toggleStatus(string $userId): void
    {
        $user = User::findOrFail($userId);
        $user->update(['is_active' => ! $user->is_active]);
        unset($this->users);
    }

    public function confirmDelete(string $userId): void
    {
        $this->userToDelete = $userId;
        $this->showDeleteModal = true;
    }

    public function deleteUser(): void
    {
        if ($this->userToDelete) {
            $user = User::findOrFail($this->userToDelete);
            $user->delete();
            $this->showDeleteModal = false;
            $this->userToDelete = null;
            unset($this->users);
            session()->flash('success', __('Benutzer wurde gelöscht.'));
        }
    }

    public function render()
    {
        return view('livewire.admin.users.index')
            ->layout('layouts.admin', ['title' => __('Benutzerverwaltung')]);
    }
}
