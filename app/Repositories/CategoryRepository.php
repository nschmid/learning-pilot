<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository extends BaseRepository
{
    protected function model(): string
    {
        return Category::class;
    }

    /**
     * Get root categories (no parent).
     */
    public function getRoots(): Collection
    {
        return $this->query
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get children of a category.
     */
    public function getChildren(string $parentId): Collection
    {
        return $this->query
            ->where('parent_id', $parentId)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get category with children.
     */
    public function getWithChildren(string $categoryId): ?Category
    {
        return $this->query
            ->with('children')
            ->find($categoryId);
    }

    /**
     * Get full category tree.
     */
    public function getTree(): Collection
    {
        return $this->query
            ->whereNull('parent_id')
            ->with('children.children')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get categories with path counts.
     */
    public function getWithPathCounts(): Collection
    {
        return $this->query
            ->withCount(['learningPaths' => fn ($q) => $q->published()])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get popular categories (most paths).
     */
    public function getPopular(int $limit = 10): Collection
    {
        return $this->query
            ->withCount(['learningPaths' => fn ($q) => $q->published()])
            ->orderBy('learning_paths_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get category ancestors (breadcrumb).
     */
    public function getAncestors(Category $category): Collection
    {
        $ancestors = collect();
        $current = $category;

        while ($current->parent_id) {
            $current = $this->find($current->parent_id);
            if ($current) {
                $ancestors->prepend($current);
            }
        }

        return $ancestors;
    }

    /**
     * Get all descendants of a category.
     */
    public function getDescendants(string $categoryId): Collection
    {
        $descendants = collect();
        $children = $this->getChildren($categoryId);

        foreach ($children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($this->getDescendants($child->id));
        }

        return $descendants;
    }

    /**
     * Check if category has children.
     */
    public function hasChildren(string $categoryId): bool
    {
        return $this->query
            ->where('parent_id', $categoryId)
            ->exists();
    }

    /**
     * Get categories for dropdown.
     */
    public function getForDropdown(): Collection
    {
        return $this->query
            ->select('id', 'name', 'parent_id')
            ->orderBy('name')
            ->get();
    }
}
