<?php

namespace App\Livewire\Admin\AI;

use App\Enums\AiFeedbackType;
use App\Models\AiFeedbackReport;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class FeedbackReview extends Component
{
    use WithPagination;

    public string $filter = 'unresolved';

    public ?string $typeFilter = null;

    public bool $showDetailModal = false;

    public ?string $selectedFeedbackId = null;

    #[Validate('nullable|string|max:1000')]
    public string $resolverNotes = '';

    #[Computed]
    public function feedback()
    {
        $query = AiFeedbackReport::with(['user:id,name,email', 'feedbackable']);

        $query = match ($this->filter) {
            'unresolved' => $query->unresolved(),
            'resolved' => $query->resolved(),
            'positive' => $query->positive(),
            'negative' => $query->negative(),
            default => $query,
        };

        if ($this->typeFilter) {
            $query->ofType(AiFeedbackType::from($this->typeFilter));
        }

        return $query->orderByDesc('created_at')->paginate(20);
    }

    #[Computed]
    public function statistics(): array
    {
        return [
            'total' => AiFeedbackReport::count(),
            'unresolved' => AiFeedbackReport::unresolved()->count(),
            'positive' => AiFeedbackReport::positive()->count(),
            'negative' => AiFeedbackReport::negative()->count(),
            'average_rating' => round(AiFeedbackReport::avg('rating') ?? 0, 1),
        ];
    }

    #[Computed]
    public function feedbackTypes(): array
    {
        return collect(AiFeedbackType::cases())
            ->map(fn ($type) => [
                'value' => $type->value,
                'label' => $type->label(),
                'count' => AiFeedbackReport::ofType($type)->count(),
            ])
            ->toArray();
    }

    #[Computed]
    public function selectedFeedback(): ?AiFeedbackReport
    {
        if (! $this->selectedFeedbackId) {
            return null;
        }

        return AiFeedbackReport::with(['user:id,name,email', 'feedbackable'])->find($this->selectedFeedbackId);
    }

    public function viewDetails(string $feedbackId): void
    {
        $this->selectedFeedbackId = $feedbackId;
        $this->resolverNotes = '';
        $this->showDetailModal = true;
    }

    public function markAsResolved(): void
    {
        $this->validate();

        $feedback = AiFeedbackReport::findOrFail($this->selectedFeedbackId);
        $feedback->resolve($this->resolverNotes ?: null);

        $this->showDetailModal = false;
        $this->selectedFeedbackId = null;
        $this->resolverNotes = '';

        session()->flash('success', __('Feedback als gelöst markiert.'));
    }

    public function reopenFeedback(string $feedbackId): void
    {
        $feedback = AiFeedbackReport::findOrFail($feedbackId);

        $feedback->update([
            'is_resolved' => false,
            'resolved_at' => null,
            'resolver_notes' => null,
        ]);

        session()->flash('success', __('Feedback wieder geöffnet.'));
    }

    public function deleteFeedback(string $feedbackId): void
    {
        AiFeedbackReport::findOrFail($feedbackId)->delete();

        if ($this->selectedFeedbackId === $feedbackId) {
            $this->showDetailModal = false;
            $this->selectedFeedbackId = null;
        }

        session()->flash('success', __('Feedback gelöscht.'));
    }

    public function closeModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedFeedbackId = null;
        $this->resolverNotes = '';
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function setTypeFilter(?string $type): void
    {
        $this->typeFilter = $type;
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.ai.feedback-review')
            ->layout('layouts.admin', ['title' => __('KI-Feedback-Übersicht')]);
    }
}
