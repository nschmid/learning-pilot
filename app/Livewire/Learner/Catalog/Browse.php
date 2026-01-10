<?php

namespace App\Livewire\Learner\Catalog;

use App\Enums\Difficulty;
use App\Models\Category;
use App\Models\LearningPath;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Browse extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public ?string $category = null;

    #[Url]
    public ?string $difficulty = null;

    #[Url]
    public string $sort = 'popular';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function updatedDifficulty(): void
    {
        $this->resetPage();
    }

    public function setCategory(?string $slug): void
    {
        $this->category = $slug;
        $this->resetPage();
    }

    public function setDifficulty(?string $value): void
    {
        $this->difficulty = $value;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->category = null;
        $this->difficulty = null;
        $this->sort = 'popular';
        $this->resetPage();
    }

    public function getCategoriesProperty()
    {
        return Category::active()
            ->root()
            ->ordered()
            ->withCount(['learningPaths' => fn ($q) => $q->published()])
            ->get();
    }

    public function getSelectedCategoryProperty()
    {
        if (! $this->category) {
            return null;
        }

        return Category::where('slug', $this->category)->first();
    }

    public function getDifficultiesProperty(): array
    {
        return Difficulty::cases();
    }

    public function getEnrolledPathIdsProperty(): array
    {
        if (! Auth::check()) {
            return [];
        }

        return Auth::user()
            ->enrollments()
            ->pluck('learning_path_id')
            ->toArray();
    }

    public function getLearningPathsProperty()
    {
        $query = LearningPath::query()
            ->published()
            ->with(['creator', 'category', 'tags'])
            ->withCount(['enrollments', 'reviews'])
            ->withAvg('reviews', 'rating');

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        // Category filter
        if ($this->category) {
            $category = Category::where('slug', $this->category)->first();
            if ($category) {
                $categoryIds = collect([$category->id]);
                // Include children categories
                $categoryIds = $categoryIds->merge(
                    $category->children()->pluck('id')
                );
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // Difficulty filter
        if ($this->difficulty) {
            $query->where('difficulty', $this->difficulty);
        }

        // Sorting
        $query = match ($this->sort) {
            'newest' => $query->orderBy('published_at', 'desc'),
            'rating' => $query->orderBy('reviews_avg_rating', 'desc'),
            'title' => $query->orderBy('title', 'asc'),
            default => $query->orderBy('enrollments_count', 'desc'), // popular
        };

        return $query->paginate(12);
    }

    public function isEnrolled(string $pathId): bool
    {
        return in_array($pathId, $this->enrolledPathIds);
    }

    public function render()
    {
        return view('livewire.learner.catalog.browse')
            ->layout('layouts.learner', ['title' => __('Kurskatalog')]);
    }
}
