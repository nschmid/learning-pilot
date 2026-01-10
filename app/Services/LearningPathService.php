<?php

namespace App\Services;

use App\Enums\Difficulty;
use App\Models\LearningPath;
use App\Models\Module;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LearningPathService
{
    /**
     * Get paginated learning paths with optional filters.
     */
    public function getPaginatedPaths(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return LearningPath::query()
            ->with(['creator', 'category', 'tags'])
            ->withCount(['enrollments', 'modules'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($filters['category_id'] ?? null, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($filters['difficulty'] ?? null, function ($query, $difficulty) {
                $query->where('difficulty', Difficulty::from($difficulty));
            })
            ->when(isset($filters['is_published']), function ($query) use ($filters) {
                $query->where('is_published', $filters['is_published']);
            })
            ->when($filters['creator_id'] ?? null, function ($query, $creatorId) {
                $query->where('creator_id', $creatorId);
            })
            ->when($filters['tag_ids'] ?? null, function ($query, $tagIds) {
                $query->whereHas('tags', function ($q) use ($tagIds) {
                    $q->whereIn('tags.id', $tagIds);
                });
            })
            ->orderBy($filters['sort_by'] ?? 'created_at', $filters['sort_dir'] ?? 'desc')
            ->paginate($perPage);
    }

    /**
     * Get published paths for catalog.
     */
    public function getPublishedPaths(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $filters['is_published'] = true;

        return $this->getPaginatedPaths($filters, $perPage);
    }

    /**
     * Get paths by creator.
     */
    public function getPathsByCreator(User $creator, array $filters = []): Collection
    {
        return LearningPath::query()
            ->with(['category', 'tags'])
            ->withCount(['enrollments', 'modules'])
            ->where('creator_id', $creator->id)
            ->when($filters['is_published'] ?? null, function ($query, $isPublished) {
                $query->where('is_published', $isPublished);
            })
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Create a new learning path.
     */
    public function create(User $creator, array $data): LearningPath
    {
        return DB::transaction(function () use ($creator, $data) {
            $path = LearningPath::create([
                'creator_id' => $creator->id,
                'team_id' => $creator->currentTeam?->id,
                'category_id' => $data['category_id'] ?? null,
                'title' => $data['title'],
                'slug' => $this->generateUniqueSlug($data['title']),
                'description' => $data['description'] ?? null,
                'objectives' => $data['objectives'] ?? [],
                'difficulty' => isset($data['difficulty']) ? Difficulty::from($data['difficulty']) : Difficulty::Beginner,
                'estimated_hours' => $data['estimated_hours'] ?? null,
                'is_published' => $data['is_published'] ?? false,
                'version' => 1,
            ]);

            // Sync tags if provided
            if (isset($data['tag_ids']) && is_array($data['tag_ids'])) {
                $path->tags()->sync($data['tag_ids']);
            }

            // Handle thumbnail upload
            if (isset($data['thumbnail'])) {
                $path->addMedia($data['thumbnail'])
                    ->toMediaCollection('thumbnail');
            }

            return $path->load(['category', 'tags']);
        });
    }

    /**
     * Update an existing learning path.
     */
    public function update(LearningPath $path, array $data): LearningPath
    {
        return DB::transaction(function () use ($path, $data) {
            $updateData = [];

            if (isset($data['title'])) {
                $updateData['title'] = $data['title'];
                // Only update slug if title changed significantly
                if ($path->title !== $data['title']) {
                    $updateData['slug'] = $this->generateUniqueSlug($data['title'], $path->id);
                }
            }

            if (isset($data['description'])) {
                $updateData['description'] = $data['description'];
            }

            if (isset($data['objectives'])) {
                $updateData['objectives'] = array_filter($data['objectives'], fn ($obj) => ! empty(trim($obj)));
            }

            if (isset($data['category_id'])) {
                $updateData['category_id'] = $data['category_id'];
            }

            if (isset($data['difficulty'])) {
                $updateData['difficulty'] = Difficulty::from($data['difficulty']);
            }

            if (isset($data['estimated_hours'])) {
                $updateData['estimated_hours'] = $data['estimated_hours'];
            }

            if (isset($data['is_published'])) {
                $updateData['is_published'] = $data['is_published'];
            }

            if (! empty($updateData)) {
                $path->update($updateData);
            }

            // Sync tags if provided
            if (isset($data['tag_ids']) && is_array($data['tag_ids'])) {
                $path->tags()->sync($data['tag_ids']);
            }

            // Handle thumbnail upload
            if (isset($data['thumbnail'])) {
                $path->clearMediaCollection('thumbnail');
                $path->addMedia($data['thumbnail'])
                    ->toMediaCollection('thumbnail');
            }

            return $path->fresh(['category', 'tags']);
        });
    }

    /**
     * Publish a learning path.
     */
    public function publish(LearningPath $path): LearningPath
    {
        // Validate path is ready for publishing
        $this->validateForPublishing($path);

        $path->update([
            'is_published' => true,
            'published_at' => now(),
        ]);

        return $path;
    }

    /**
     * Unpublish a learning path.
     */
    public function unpublish(LearningPath $path): LearningPath
    {
        $path->update(['is_published' => false]);

        return $path;
    }

    /**
     * Duplicate a learning path.
     */
    public function duplicate(LearningPath $path, ?User $newCreator = null): LearningPath
    {
        return DB::transaction(function () use ($path, $newCreator) {
            $creator = $newCreator ?? $path->creator;

            // Create the duplicate path
            $newPath = $path->replicate([
                'id',
                'slug',
                'is_published',
                'published_at',
                'version',
                'created_at',
                'updated_at',
            ]);

            $newPath->creator_id = $creator->id;
            $newPath->team_id = $creator->currentTeam?->id;
            $newPath->title = $path->title.' (Kopie)';
            $newPath->slug = $this->generateUniqueSlug($newPath->title);
            $newPath->is_published = false;
            $newPath->version = 1;
            $newPath->save();

            // Copy tags
            $newPath->tags()->sync($path->tags->pluck('id'));

            // Copy thumbnail
            if ($path->hasMedia('thumbnail')) {
                $media = $path->getFirstMedia('thumbnail');
                $newPath->addMedia($media->getPath())
                    ->preservingOriginal()
                    ->toMediaCollection('thumbnail');
            }

            // Copy modules and steps
            foreach ($path->modules as $module) {
                $this->duplicateModule($module, $newPath);
            }

            return $newPath->load(['category', 'tags', 'modules.steps']);
        });
    }

    /**
     * Duplicate a module to a new path.
     */
    protected function duplicateModule(Module $module, LearningPath $newPath): Module
    {
        $newModule = $module->replicate(['id', 'learning_path_id', 'created_at', 'updated_at']);
        $newModule->learning_path_id = $newPath->id;
        $newModule->save();

        // Copy steps
        foreach ($module->steps as $step) {
            $newStep = $step->replicate(['id', 'module_id', 'created_at', 'updated_at']);
            $newStep->module_id = $newModule->id;
            $newStep->save();

            // Copy materials, tasks, or assessments depending on step type
            $this->duplicateStepContent($step, $newStep);
        }

        return $newModule;
    }

    /**
     * Duplicate step content (materials, tasks, assessments).
     */
    protected function duplicateStepContent($originalStep, $newStep): void
    {
        // Copy materials
        foreach ($originalStep->materials as $material) {
            $newMaterial = $material->replicate(['id', 'step_id', 'created_at', 'updated_at']);
            $newMaterial->step_id = $newStep->id;
            $newMaterial->save();
        }

        // Copy task if exists
        if ($originalStep->task) {
            $newTask = $originalStep->task->replicate(['id', 'step_id', 'created_at', 'updated_at']);
            $newTask->step_id = $newStep->id;
            $newTask->save();
        }

        // Copy assessment if exists
        if ($originalStep->assessment) {
            $newAssessment = $originalStep->assessment->replicate([
                'id', 'step_id', 'created_at', 'updated_at',
            ]);
            $newAssessment->step_id = $newStep->id;
            $newAssessment->save();

            // Copy questions
            foreach ($originalStep->assessment->questions as $question) {
                $newQuestion = $question->replicate([
                    'id', 'assessment_id', 'created_at', 'updated_at',
                ]);
                $newQuestion->assessment_id = $newAssessment->id;
                $newQuestion->save();

                // Copy answer options
                foreach ($question->options as $option) {
                    $newOption = $option->replicate([
                        'id', 'question_id', 'created_at', 'updated_at',
                    ]);
                    $newOption->question_id = $newQuestion->id;
                    $newOption->save();
                }
            }
        }
    }

    /**
     * Delete a learning path.
     */
    public function delete(LearningPath $path): bool
    {
        return DB::transaction(function () use ($path) {
            // Delete related media
            $path->clearMediaCollection('thumbnail');

            // Soft delete the path (cascade will handle modules, steps, etc.)
            return $path->delete();
        });
    }

    /**
     * Get path statistics.
     */
    public function getStatistics(LearningPath $path): array
    {
        $enrollments = $path->enrollments();

        return [
            'total_enrollments' => $enrollments->count(),
            'active_enrollments' => (clone $enrollments)->where('status', 'active')->count(),
            'completed_enrollments' => (clone $enrollments)->where('status', 'completed')->count(),
            'avg_progress' => round((clone $enrollments)->avg('progress_percent') ?? 0),
            'avg_time_hours' => round(((clone $enrollments)->avg('total_time_spent_seconds') ?? 0) / 3600, 1),
            'total_modules' => $path->modules()->count(),
            'total_steps' => $path->modules()->withCount('steps')->get()->sum('steps_count'),
            'total_points' => $path->modules()
                ->with('steps')
                ->get()
                ->flatMap(fn ($m) => $m->steps)
                ->sum('points_value'),
        ];
    }

    /**
     * Validate path is ready for publishing.
     *
     * @throws \InvalidArgumentException
     */
    protected function validateForPublishing(LearningPath $path): void
    {
        $errors = [];

        if (empty($path->title)) {
            $errors[] = __('Titel ist erforderlich');
        }

        if (empty($path->description)) {
            $errors[] = __('Beschreibung ist erforderlich');
        }

        if ($path->modules()->count() === 0) {
            $errors[] = __('Mindestens ein Modul ist erforderlich');
        }

        $hasSteps = $path->modules()
            ->whereHas('steps')
            ->exists();

        if (! $hasSteps) {
            $errors[] = __('Mindestens ein Schritt ist erforderlich');
        }

        if (! empty($errors)) {
            throw new \InvalidArgumentException(implode(', ', $errors));
        }
    }

    /**
     * Generate a unique slug.
     */
    protected function generateUniqueSlug(string $title, ?string $excludeId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (LearningPath::where('slug', $slug)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists()
        ) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Increment path version.
     */
    public function incrementVersion(LearningPath $path): LearningPath
    {
        $path->increment('version');

        return $path;
    }

    /**
     * Get related paths (same category or tags).
     */
    public function getRelatedPaths(LearningPath $path, int $limit = 5): Collection
    {
        return LearningPath::query()
            ->where('id', '!=', $path->id)
            ->where('is_published', true)
            ->where(function ($query) use ($path) {
                // Same category
                if ($path->category_id) {
                    $query->where('category_id', $path->category_id);
                }

                // Same tags
                $tagIds = $path->tags->pluck('id');
                if ($tagIds->isNotEmpty()) {
                    $query->orWhereHas('tags', function ($q) use ($tagIds) {
                        $q->whereIn('tags.id', $tagIds);
                    });
                }
            })
            ->withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->take($limit)
            ->get();
    }
}
