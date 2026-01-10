<?php

namespace App\Livewire\Instructor\LearningPaths;

use App\Models\LearningPath;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
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
    public string $sort = 'updated_at';

    #[Url]
    public string $direction = 'desc';

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

    public function sortBy(string $column): void
    {
        if ($this->sort === $column) {
            $this->direction = $this->direction === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sort = $column;
            $this->direction = 'asc';
        }
    }

    #[Computed]
    public function paths(): LengthAwarePaginator
    {
        return LearningPath::where('creator_id', Auth::id())
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status === 'published', fn ($q) => $q->where('is_published', true))
            ->when($this->status === 'draft', fn ($q) => $q->where('is_published', false))
            ->withCount(['modules', 'steps', 'enrollments'])
            ->orderBy($this->sort, $this->direction)
            ->paginate(12);
    }

    public function duplicate(LearningPath $path): void
    {
        if ($path->creator_id !== Auth::id()) {
            return;
        }

        $newPath = $path->replicate();
        $newPath->title = $path->title . ' (Kopie)';
        $newPath->slug = null; // Will be auto-generated
        $newPath->is_published = false;
        $newPath->save();

        // Duplicate modules and steps
        foreach ($path->modules as $module) {
            $newModule = $module->replicate();
            $newModule->learning_path_id = $newPath->id;
            $newModule->save();

            foreach ($module->steps as $step) {
                $newStep = $step->replicate();
                $newStep->module_id = $newModule->id;
                $newStep->save();
            }
        }

        unset($this->paths);
        session()->flash('success', __('Lernpfad wurde dupliziert.'));
    }

    public function confirmDelete(string $pathId): void
    {
        $this->pathToDelete = $pathId;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->pathToDelete = null;
        $this->showDeleteModal = false;
    }

    public function delete(): void
    {
        if (! $this->pathToDelete) {
            return;
        }

        $path = LearningPath::where('id', $this->pathToDelete)
            ->where('creator_id', Auth::id())
            ->first();

        if ($path) {
            $path->delete();
            unset($this->paths);
            session()->flash('success', __('Lernpfad wurde gelöscht.'));
        }

        $this->cancelDelete();
    }

    public function togglePublish(LearningPath $path): void
    {
        if ($path->creator_id !== Auth::id()) {
            return;
        }

        $path->update(['is_published' => ! $path->is_published]);
        unset($this->paths);
    }

    public function render()
    {
        return view('livewire.instructor.learning-paths.index')
            ->layout('layouts.instructor', ['title' => __('Meine Lernpfade')]);
    }
}
