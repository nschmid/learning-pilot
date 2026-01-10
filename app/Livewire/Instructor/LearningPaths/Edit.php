<?php

namespace App\Livewire\Instructor\LearningPaths;

use App\Enums\Difficulty;
use App\Models\Category;
use App\Models\LearningPath;
use App\Models\Tag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public LearningPath $path;

    public string $title = '';

    public string $description = '';

    public array $objectives = [''];

    public string $difficulty = 'beginner';

    public ?string $categoryId = null;

    public array $selectedTags = [];

    public ?int $estimatedHours = null;

    public $thumbnail = null;

    public ?string $existingThumbnail = null;

    public function mount(LearningPath $path): void
    {
        if ($path->creator_id !== Auth::id()) {
            abort(403);
        }

        $this->path = $path->load(['category', 'tags']);

        $this->title = $path->title;
        $this->description = $path->description ?? '';
        $this->objectives = ! empty($path->objectives) ? $path->objectives : [''];
        $this->difficulty = $path->difficulty?->value ?? 'beginner';
        $this->categoryId = $path->category_id;
        $this->selectedTags = $path->tags->pluck('id')->toArray();
        $this->estimatedHours = $path->estimated_hours;
        $this->existingThumbnail = $path->getFirstMediaUrl('thumbnail');
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'objectives' => ['array', 'min:1'],
            'objectives.*' => ['nullable', 'string', 'max:500'],
            'difficulty' => ['required', 'in:beginner,intermediate,advanced,expert'],
            'categoryId' => ['nullable', 'exists:categories,id'],
            'selectedTags' => ['array'],
            'estimatedHours' => ['nullable', 'integer', 'min:1', 'max:500'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required' => __('Bitte gib einen Titel ein.'),
            'title.min' => __('Der Titel muss mindestens 3 Zeichen lang sein.'),
            'description.required' => __('Bitte gib eine Beschreibung ein.'),
            'description.min' => __('Die Beschreibung muss mindestens 10 Zeichen lang sein.'),
        ];
    }

    #[Computed]
    public function categories(): Collection
    {
        return Category::active()
            ->root()
            ->with('children')
            ->ordered()
            ->get();
    }

    #[Computed]
    public function tags(): Collection
    {
        return Tag::orderBy('name')->get();
    }

    #[Computed]
    public function difficulties(): array
    {
        return collect(Difficulty::cases())
            ->map(fn ($d) => ['value' => $d->value, 'label' => $d->label()])
            ->all();
    }

    public function addObjective(): void
    {
        $this->objectives[] = '';
    }

    public function removeObjective(int $index): void
    {
        if (count($this->objectives) > 1) {
            unset($this->objectives[$index]);
            $this->objectives = array_values($this->objectives);
        }
    }

    public function removeThumbnail(): void
    {
        $this->path->clearMediaCollection('thumbnail');
        $this->existingThumbnail = null;
    }

    public function save(): void
    {
        $validated = $this->validate();

        // Filter empty objectives
        $objectives = array_filter($validated['objectives'], fn ($obj) => ! empty(trim($obj)));

        $this->path->update([
            'category_id' => $validated['categoryId'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'objectives' => array_values($objectives),
            'difficulty' => Difficulty::from($validated['difficulty']),
            'estimated_hours' => $validated['estimatedHours'],
        ]);

        // Handle thumbnail upload
        if ($this->thumbnail) {
            $this->path->clearMediaCollection('thumbnail');
            $this->path->addMedia($this->thumbnail->getRealPath())
                ->usingFileName($this->thumbnail->getClientOriginalName())
                ->toMediaCollection('thumbnail');
        }

        // Sync tags
        $this->path->tags()->sync($this->selectedTags);

        session()->flash('success', __('Änderungen wurden gespeichert.'));
        $this->redirect(route('instructor.paths.show', $this->path), navigate: true);
    }

    public function render()
    {
        return view('livewire.instructor.learning-paths.edit')
            ->layout('layouts.instructor', ['title' => __('Lernpfad bearbeiten')]);
    }
}
