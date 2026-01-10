<?php

namespace App\Livewire\Learner\Bookmarks;

use App\Models\Bookmark;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function getBookmarksProperty()
    {
        $query = Bookmark::query()
            ->where('user_id', Auth::id())
            ->with([
                'step.module.learningPath',
            ])
            ->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->whereHas('step', function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhereHas('module', function ($mq) {
                        $mq->where('title', 'like', "%{$this->search}%");
                    })
                    ->orWhereHas('module.learningPath', function ($pq) {
                        $pq->where('title', 'like', "%{$this->search}%");
                    });
            });
        }

        return $query->paginate(20);
    }

    public function removeBookmark(string $stepId): void
    {
        Bookmark::where('user_id', Auth::id())
            ->where('step_id', $stepId)
            ->delete();

        $this->dispatch('bookmark-removed');
    }

    public function render()
    {
        return view('livewire.learner.bookmarks.index')
            ->layout('layouts.learner', ['title' => __('Lesezeichen')]);
    }
}
