<?php

namespace Database\Factories;

use App\Enums\UnlockCondition;
use App\Models\LearningPath;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Module>
 */
class ModuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'learning_path_id' => LearningPath::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'position' => fake()->numberBetween(1, 10),
            'unlock_condition' => UnlockCondition::Sequential,
            'unlock_value' => null,
            'is_required' => true,
        ];
    }

    /**
     * Indicate that the module is optional.
     */
    public function optional(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_required' => false,
        ]);
    }

    /**
     * Set a specific unlock condition.
     */
    public function unlockCondition(UnlockCondition $condition, mixed $value = null): static
    {
        return $this->state(fn (array $attributes) => [
            'unlock_condition' => $condition,
            'unlock_value' => $value,
        ]);
    }

    /**
     * Create for a specific learning path.
     */
    public function forPath(LearningPath $path): static
    {
        return $this->state(fn (array $attributes) => [
            'learning_path_id' => $path->id,
        ]);
    }

    /**
     * Set position.
     */
    public function position(int $position): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => $position,
        ]);
    }
}
