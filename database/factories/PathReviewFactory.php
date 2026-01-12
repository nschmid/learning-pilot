<?php

namespace Database\Factories;

use App\Models\LearningPath;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PathReview>
 */
class PathReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'learning_path_id' => LearningPath::factory(),
            'rating' => fake()->numberBetween(1, 5),
            'review_text' => fake()->paragraph(),
            'is_approved' => false,
        ];
    }

    /**
     * Indicate that the review is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => true,
        ]);
    }

    /**
     * Set a specific rating.
     */
    public function rating(int $rating): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => $rating,
        ]);
    }

    /**
     * Create for specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Create for specific learning path.
     */
    public function forPath(LearningPath $path): static
    {
        return $this->state(fn (array $attributes) => [
            'learning_path_id' => $path->id,
        ]);
    }
}
