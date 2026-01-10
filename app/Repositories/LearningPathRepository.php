<?php

namespace App\Repositories;

use App\Enums\Difficulty;
use App\Models\LearningPath;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class LearningPathRepository extends BaseRepository
{
    protected function model(): string
    {
        return LearningPath::class;
    }

    /**
     * Get published learning paths.
     */
    public function getPublished(): self
    {
        $this->query = $this->query->published();

        return $this;
    }

    /**
     * Get paths by creator.
     */
    public function getByCreator(User $user): Collection
    {
        return $this->query
            ->where('creator_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Get paths by category.
     */
    public function getByCategory(string $categoryId): self
    {
        $this->query = $this->query->where('category_id', $categoryId);

        return $this;
    }

    /**
     * Get paths by difficulty.
     */
    public function getByDifficulty(Difficulty $difficulty): self
    {
        $this->query = $this->query->where('difficulty', $difficulty);

        return $this;
    }

    /**
     * Get paths with tags.
     */
    public function getByTags(array $tagIds): self
    {
        $this->query = $this->query->whereHas('tags', function ($query) use ($tagIds) {
            $query->whereIn('tags.id', $tagIds);
        });

        return $this;
    }

    /**
     * Search paths by title or description.
     */
    public function search(string $term): self
    {
        $this->query = $this->query->where(function ($query) use ($term) {
            $query->where('title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });

        return $this;
    }

    /**
     * Get paths with full details for catalog.
     */
    public function withCatalogDetails(): self
    {
        $this->query = $this->query->with([
            'creator:id,name',
            'category:id,name,slug',
            'tags:id,name,slug',
        ])->withCount(['modules', 'enrollments']);

        return $this;
    }

    /**
     * Get paths with learning structure.
     */
    public function withStructure(): self
    {
        $this->query = $this->query->with([
            'modules' => fn ($q) => $q->ordered()->with([
                'steps' => fn ($q) => $q->ordered(),
            ]),
        ]);

        return $this;
    }

    /**
     * Get popular paths (most enrollments).
     */
    public function popular(int $limit = 10): Collection
    {
        return $this->query
            ->published()
            ->withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recently published paths.
     */
    public function recent(int $limit = 10): Collection
    {
        return $this->query
            ->published()
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get paths for a team.
     */
    public function getByTeam(string $teamId): self
    {
        $this->query = $this->query->where('team_id', $teamId);

        return $this;
    }

    /**
     * Get path statistics.
     */
    public function getStats(string $pathId): array
    {
        $path = $this->with(['enrollments', 'modules.steps'])->find($pathId);

        if (! $path) {
            return [];
        }

        $enrollments = $path->enrollments;

        return [
            'total_enrollments' => $enrollments->count(),
            'completed_enrollments' => $enrollments->where('status', 'completed')->count(),
            'average_progress' => $enrollments->avg('progress_percent') ?? 0,
            'total_modules' => $path->modules->count(),
            'total_steps' => $path->modules->sum(fn ($m) => $m->steps->count()),
            'average_rating' => $path->reviews()->avg('rating') ?? 0,
        ];
    }

    /**
     * Duplicate a learning path.
     */
    public function duplicate(string $pathId, User $newCreator): ?LearningPath
    {
        $original = $this->with(['modules.steps.materials', 'modules.steps.assessments', 'tags'])
            ->find($pathId);

        if (! $original) {
            return null;
        }

        $newPath = $original->replicate();
        $newPath->creator_id = $newCreator->id;
        $newPath->title = $original->title . ' (Kopie)';
        $newPath->slug = null; // Let the model generate a new slug
        $newPath->is_published = false;
        $newPath->published_at = null;
        $newPath->version = 1;
        $newPath->save();

        // Copy tags
        $newPath->tags()->attach($original->tags->pluck('id'));

        // Copy modules and steps
        foreach ($original->modules as $module) {
            $newModule = $module->replicate();
            $newModule->learning_path_id = $newPath->id;
            $newModule->save();

            foreach ($module->steps as $step) {
                $newStep = $step->replicate();
                $newStep->module_id = $newModule->id;
                $newStep->save();

                // Copy materials
                foreach ($step->materials as $material) {
                    $newMaterial = $material->replicate();
                    $newMaterial->step_id = $newStep->id;
                    $newMaterial->save();
                }
            }
        }

        return $newPath;
    }
}
