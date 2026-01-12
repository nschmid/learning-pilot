<?php

namespace Database\Factories;

use App\Enums\Difficulty;
use App\Models\Category;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LearningPath>
 */
class LearningPathFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'creator_id' => User::factory(),
            'team_id' => Team::factory(),
            'category_id' => Category::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraphs(2, true),
            'objectives' => [
                fake()->sentence(),
                fake()->sentence(),
                fake()->sentence(),
            ],
            'difficulty' => fake()->randomElement(Difficulty::cases()),
            'estimated_hours' => fake()->numberBetween(1, 100),
            'is_published' => false,
            'is_featured' => false,
            'version' => 1,
            'metadata' => [],
        ];
    }

    /**
     * Indicate that the learning path is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
            'published_at' => now(),
        ]);
    }

    /**
     * Indicate that the learning path is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Set a specific difficulty level.
     */
    public function difficulty(Difficulty $difficulty): static
    {
        return $this->state(fn (array $attributes) => [
            'difficulty' => $difficulty,
        ]);
    }

    /**
     * Create for a specific team.
     */
    public function forTeam(Team $team): static
    {
        return $this->state(fn (array $attributes) => [
            'team_id' => $team->id,
        ]);
    }

    /**
     * Create for a specific creator.
     */
    public function createdBy(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'creator_id' => $user->id,
        ]);
    }
}
