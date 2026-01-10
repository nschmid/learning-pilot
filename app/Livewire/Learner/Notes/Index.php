<?php

namespace App\Livewire\Learner\Notes;

use App\Models\UserNote;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public ?string $editingNoteId = null;

    public string $editingContent = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function getNotesProperty()
    {
        $query = UserNote::query()
            ->where('user_id', Auth::id())
            ->with([
                'step.module.learningPath',
            ])
            ->orderBy('updated_at', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('content', 'like', "%{$this->search}%")
                    ->orWhereHas('step', function ($sq) {
                        $sq->where('title', 'like', "%{$this->search}%");
                    })
                    ->orWhereHas('step.module.learningPath', function ($pq) {
                        $pq->where('title', 'like', "%{$this->search}%");
                    });
            });
        }

        return $query->paginate(20);
    }

    public function startEditing(string $noteId): void
    {
        $note = UserNote::where('id', $noteId)
            ->where('user_id', Auth::id())
            ->first();

        if ($note) {
            $this->editingNoteId = $noteId;
            $this->editingContent = $note->content;
        }
    }

    public function saveNote(): void
    {
        if (! $this->editingNoteId) {
            return;
        }

        UserNote::where('id', $this->editingNoteId)
            ->where('user_id', Auth::id())
            ->update([
                'content' => $this->editingContent,
            ]);

        $this->cancelEditing();
        $this->dispatch('note-saved');
    }

    public function cancelEditing(): void
    {
        $this->editingNoteId = null;
        $this->editingContent = '';
    }

    public function deleteNote(string $noteId): void
    {
        UserNote::where('id', $noteId)
            ->where('user_id', Auth::id())
            ->delete();

        $this->dispatch('note-deleted');
    }

    public function render()
    {
        return view('livewire.learner.notes.index')
            ->layout('layouts.learner', ['title' => __('Notizen')]);
    }
}
