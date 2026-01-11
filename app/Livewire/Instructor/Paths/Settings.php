<?php

namespace App\Livewire\Instructor\Paths;

use App\Enums\Difficulty;
use App\Models\Category;
use App\Models\LearningPath;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Settings extends Component
{
    public LearningPath $path;

    // Basic Settings
    public bool $isPublished = false;
    public bool $isFeatured = false;
    public string $difficulty = 'beginner';
    public ?string $categoryId = null;
    public array $selectedTags = [];
    public ?int $estimatedHours = null;

    // Advanced Settings (stored in metadata)
    public bool $requiresApproval = false;
    public ?int $maxEnrollments = null;
    public bool $certificateEnabled = true;
    public ?int $certificateValidityDays = null;

    public function mount(LearningPath $path): void
    {
        // Verify ownership
        if ($path->creator_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403);
        }

        $this->path = $path;

        // Load basic settings
        $this->isPublished = $path->is_published;
        $this->isFeatured = $path->is_featured;
        $this->difficulty = $path->difficulty?->value ?? 'beginner';
        $this->categoryId = $path->category_id;
        $this->selectedTags = $path->tags->pluck('id')->toArray();
        $this->estimatedHours = $path->estimated_hours;

        // Load advanced settings from metadata
        $metadata = $path->metadata ?? [];
        $this->requiresApproval = $metadata['requires_approval'] ?? false;
        $this->maxEnrollments = $metadata['max_enrollments'] ?? null;
        $this->certificateEnabled = $metadata['certificate_enabled'] ?? true;
        $this->certificateValidityDays = $metadata['certificate_validity_days'] ?? null;
    }

    #[Computed]
    public function categories()
    {
        return Category::orderBy('name')->get();
    }

    #[Computed]
    public function tags()
    {
        return Tag::orderBy('name')->get();
    }

    #[Computed]
    public function difficulties(): array
    {
        return array_map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ], Difficulty::cases());
    }

    public function save(): void
    {
        $this->validate([
            'difficulty' => 'required|in:' . implode(',', array_column(Difficulty::cases(), 'value')),
            'categoryId' => 'nullable|exists:categories,id',
            'estimatedHours' => 'nullable|integer|min:1',
            'maxEnrollments' => 'nullable|integer|min:1',
            'certificateValidityDays' => 'nullable|integer|min:1',
        ]);

        $this->path->update([
            'is_published' => $this->isPublished,
            'is_featured' => $this->isFeatured,
            'difficulty' => $this->difficulty,
            'category_id' => $this->categoryId ?: null,
            'estimated_hours' => $this->estimatedHours,
            'published_at' => $this->isPublished && ! $this->path->published_at ? now() : $this->path->published_at,
            'metadata' => array_merge($this->path->metadata ?? [], [
                'requires_approval' => $this->requiresApproval,
                'max_enrollments' => $this->maxEnrollments,
                'certificate_enabled' => $this->certificateEnabled,
                'certificate_validity_days' => $this->certificateValidityDays,
            ]),
        ]);

        // Sync tags
        $this->path->tags()->sync($this->selectedTags);

        $this->dispatch('saved');
        session()->flash('success', __('Einstellungen gespeichert.'));
    }

    public function togglePublish(): void
    {
        $this->isPublished = ! $this->isPublished;
        $this->save();
    }

    public function render()
    {
        return view('livewire.instructor.paths.settings')
            ->layout('layouts.instructor', ['title' => __('Einstellungen') . ' - ' . $this->path->title]);
    }
}
