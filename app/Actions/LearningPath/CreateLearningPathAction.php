<?php

namespace App\Actions\LearningPath;

use App\Enums\Difficulty;
use App\Models\LearningPath;
use App\Models\User;
use Illuminate\Support\Str;

class CreateLearningPathAction
{
    /**
     * Create a new learning path.
     */
    public function execute(User $creator, array $data): LearningPath
    {
        $path = LearningPath::create([
            'creator_id' => $creator->id,
            'team_id' => $data['team_id'] ?? $creator->currentTeam?->id,
            'category_id' => $data['category_id'] ?? null,
            'title' => $data['title'],
            'slug' => $data['slug'] ?? Str::slug($data['title']),
            'description' => $data['description'] ?? null,
            'difficulty' => $data['difficulty'] ?? Difficulty::Beginner,
            'thumbnail' => $data['thumbnail'] ?? null,
            'estimated_hours' => $data['estimated_hours'] ?? null,
            'is_published' => false,
            'version' => 1,
        ]);

        // Attach tags if provided
        if (! empty($data['tag_ids'])) {
            $path->tags()->attach($data['tag_ids']);
        }

        activity()
            ->performedOn($path)
            ->causedBy($creator)
            ->log('created');

        return $path;
    }
}
