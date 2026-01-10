<?php

namespace App\Livewire\Admin\Paths;

use App\Models\Category;
use App\Models\LearningPath;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
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
    public string $status = '';

    #[Url]
    public string $categoryId = '';

    #[Url]
    public string $creatorId = '';

    #[Url]
    public string $sortBy = 'created_at';

    #[Url]
    public string $sortDir = 'desc';

    public bool $showDeleteModal = false;

    public ?string $pathToDelete = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryId(): void
    {
        $this->resetPage();
    }

    public function updatedCreatorId(): void
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
    public function paths(): LengthAwarePaginator
    {
        return LearningPath::query()
            ->with(['creator', 'category'])
            ->withCount('enrollments')
            ->when($this->search, function ($query) {
                $query->where('title', 'like', "%{$this->search}%");
            })
            ->when($this->status !== '', function ($query) {
                $query->where('is_published', $this->status === 'published');
            })
            ->when($this->categoryId, function ($query) {
                $query->where('category_id', $this->categoryId);
            })
            ->when($this->creatorId, function ($query) {
                $query->where('creator_id', $this->creatorId);
            })
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(15);
    }

    #[Computed]
    public function categories(): Collection
    {
        return Category::active()->root()->with('children')->ordered()->get();
    }

    #[Computed]
    public function creators(): Collection
    {
        return User::whereHas('createdPaths')->orderBy('name')->get();
    }

    public function togglePublished(string $pathId): void
    {
        $path = LearningPath::findOrFail($pathId);
        $path->update(['is_published' => ! $path->is_published]);
        unset($this->paths);
    }

    public function confirmDelete(string $pathId): void
    {
        $this->pathToDelete = $pathId;
        $this->showDeleteModal = true;
    }

    public function deletePath(): void
    {
        if ($this->pathToDelete) {
            $path = LearningPath::findOrFail($this->pathToDelete);
            $path->delete();
            $this->showDeleteModal = false;
            $this->pathToDelete = null;
            unset($this->paths);
            session()->flash('success', __('Lernpfad wurde gelöscht.'));
        }
    }

    public function render()
    {
        return view('livewire.admin.paths.index')
            ->layout('layouts.admin', ['title' => __('Lernpfade verwalten')]);
    }
}
