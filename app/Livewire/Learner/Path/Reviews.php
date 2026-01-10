<?php

namespace App\Livewire\Learner\Path;

use App\Models\LearningPath;
use App\Models\PathReview;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Reviews extends Component
{
    use WithPagination;

    public LearningPath $path;

    #[Url]
    public string $sort = 'newest';

    // Review form
    public int $rating = 0;
    public string $reviewText = '';
    public bool $showReviewForm = false;

    public function mount(LearningPath $path): void
    {
        $this->path = $path;
    }

    #[Computed]
    public function reviews()
    {
        $query = $this->path->reviews()->with('user');

        $query = match ($this->sort) {
            'oldest' => $query->oldest(),
            'highest' => $query->orderByDesc('rating'),
            'lowest' => $query->orderBy('rating'),
            default => $query->latest(),
        };

        return $query->paginate(10);
    }

    #[Computed]
    public function averageRating(): float
    {
        return round($this->path->reviews()->avg('rating') ?? 0, 1);
    }

    #[Computed]
    public function ratingDistribution(): array
    {
        $distribution = [];
        $total = $this->path->reviews()->count();

        for ($i = 5; $i >= 1; $i--) {
            $count = $this->path->reviews()->where('rating', $i)->count();
            $distribution[$i] = [
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100) : 0,
            ];
        }

        return $distribution;
    }

    #[Computed]
    public function userReview(): ?PathReview
    {
        return $this->path->reviews()
            ->where('user_id', Auth::id())
            ->first();
    }

    #[Computed]
    public function canReview(): bool
    {
        // User must be enrolled and have made some progress
        $enrollment = Auth::user()->enrollments()
            ->where('learning_path_id', $this->path->id)
            ->first();

        return $enrollment && $enrollment->progress_percent >= 20;
    }

    public function setRating(int $rating): void
    {
        $this->rating = $rating;
    }

    public function submitReview(): void
    {
        if (!$this->canReview) {
            return;
        }

        $this->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'reviewText' => ['nullable', 'string', 'max:2000'],
        ]);

        PathReview::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'learning_path_id' => $this->path->id,
            ],
            [
                'rating' => $this->rating,
                'review_text' => $this->reviewText,
            ]
        );

        $this->reset(['rating', 'reviewText', 'showReviewForm']);
        $this->dispatch('review-submitted');
        session()->flash('message', __('Deine Bewertung wurde gespeichert.'));
    }

    public function editReview(): void
    {
        $review = $this->userReview;
        if ($review) {
            $this->rating = $review->rating;
            $this->reviewText = $review->review_text ?? '';
            $this->showReviewForm = true;
        }
    }

    public function deleteReview(): void
    {
        $this->userReview?->delete();
        $this->reset(['rating', 'reviewText', 'showReviewForm']);
        session()->flash('message', __('Deine Bewertung wurde gelöscht.'));
    }

    public function render()
    {
        return view('livewire.learner.path.reviews')
            ->layout('layouts.learner', ['title' => __('Bewertungen') . ' - ' . $this->path->title]);
    }
}
