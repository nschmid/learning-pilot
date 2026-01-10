<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Index extends Component
{
    public string $search = '';

    // Create/Edit form
    public bool $showModal = false;

    public ?string $editingId = null;

    public string $name = '';

    public string $slug = '';

    public string $description = '';

    public ?string $parentId = null;

    public bool $isActive = true;

    public int $sortOrder = 0;

    // Delete modal
    public bool $showDeleteModal = false;

    public ?string $categoryToDelete = null;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug'.($this->editingId ? ','.$this->editingId : '')],
            'description' => ['nullable', 'string', 'max:1000'],
            'parentId' => ['nullable', 'exists:categories,id'],
            'isActive' => ['boolean'],
            'sortOrder' => ['integer', 'min:0'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => __('Bitte gib einen Namen ein.'),
            'slug.required' => __('Bitte gib einen Slug ein.'),
            'slug.unique' => __('Dieser Slug wird bereits verwendet.'),
        ];
    }

    public function updatedName(): void
    {
        if (! $this->editingId) {
            $this->slug = Str::slug($this->name);
        }
    }

    #[Computed]
    public function categories(): Collection
    {
        return Category::query()
            ->with('children')
            ->withCount('learningPaths')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function parentCategories(): Collection
    {
        return Category::whereNull('parent_id')
            ->where('id', '!=', $this->editingId)
            ->orderBy('name')
            ->get();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(string $categoryId): void
    {
        $category = Category::findOrFail($categoryId);
        $this->editingId = $category->id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description ?? '';
        $this->parentId = $category->parent_id;
        $this->isActive = $category->is_active;
        $this->sortOrder = $category->sort_order;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        $data = [
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'],
            'parent_id' => $validated['parentId'],
            'is_active' => $this->isActive,
            'sort_order' => $validated['sortOrder'],
        ];

        if ($this->editingId) {
            $category = Category::findOrFail($this->editingId);
            $category->update($data);
            session()->flash('success', __('Kategorie wurde aktualisiert.'));
        } else {
            Category::create($data);
            session()->flash('success', __('Kategorie wurde erstellt.'));
        }

        $this->closeModal();
        unset($this->categories);
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->slug = '';
        $this->description = '';
        $this->parentId = null;
        $this->isActive = true;
        $this->sortOrder = 0;
        $this->resetValidation();
    }

    public function toggleActive(string $categoryId): void
    {
        $category = Category::findOrFail($categoryId);
        $category->update(['is_active' => ! $category->is_active]);
        unset($this->categories);
    }

    public function confirmDelete(string $categoryId): void
    {
        $this->categoryToDelete = $categoryId;
        $this->showDeleteModal = true;
    }

    public function deleteCategory(): void
    {
        if ($this->categoryToDelete) {
            $category = Category::findOrFail($this->categoryToDelete);

            // Move children to root
            Category::where('parent_id', $category->id)->update(['parent_id' => null]);

            $category->delete();

            $this->showDeleteModal = false;
            $this->categoryToDelete = null;
            unset($this->categories);
            session()->flash('success', __('Kategorie wurde gelöscht.'));
        }
    }

    public function render()
    {
        return view('livewire.admin.categories.index')
            ->layout('layouts.admin', ['title' => __('Kategorien')]);
    }
}
