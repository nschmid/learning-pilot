<?php

namespace App\Livewire\Admin\Teams;

use App\Models\Team;
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
    public string $sortBy = 'created_at';

    #[Url]
    public string $sortDir = 'desc';

    public bool $showDeleteModal = false;

    public ?int $teamToDelete = null;

    public function updatedSearch(): void
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
    public function teams(): LengthAwarePaginator
    {
        return Team::query()
            ->with('owner')
            ->withCount('users')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(15);
    }

    public function confirmDelete(int $teamId): void
    {
        $this->teamToDelete = $teamId;
        $this->showDeleteModal = true;
    }

    public function deleteTeam(): void
    {
        if ($this->teamToDelete) {
            $team = Team::findOrFail($this->teamToDelete);
            $team->delete();
            $this->showDeleteModal = false;
            $this->teamToDelete = null;
            unset($this->teams);
            session()->flash('success', __('Team wurde gelöscht.'));
        }
    }

    public function render()
    {
        return view('livewire.admin.teams.index')
            ->layout('layouts.admin', ['title' => __('Teams verwalten')]);
    }
}
