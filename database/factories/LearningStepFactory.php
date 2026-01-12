<?php

namespace Database\Factories;

use App\Enums\StepType;
use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LearningStep>
 */
class LearningStepFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'module_id' => Module::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'step_type' => StepType::Material,
            'position' => fake()->numberBetween(1, 10),
            'points_value' => fake()->randomElement([10, 20, 30, 50]),
            'estimated_minutes' => fake()->numberBetween(5, 60),
            'is_required' => true,
            'is_preview' => false,
        ];
    }

    /**
     * Create a material step.
     */
    public function material(): static
    {
        return $this->state(fn (array $attributes) => [
            'step_type' => StepType::Material,
        ]);
    }

    /**
     * Create a task step.
     */
    public function task(): static
    {
        return $this->state(fn (array $attributes) => [
            'step_type' => StepType::Task,
        ]);
    }

    /**
     * Create an assessment step.
     */
    public function assessment(): static
    {
        return $this->state(fn (array $attributes) => [
            'step_type' => StepType::Assessment,
        ]);
    }

    /**
     * Create an optional step.
     */
    public function optional(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_required' => false,
        ]);
    }

    /**
     * Create a preview step.
     */
    public function preview(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_preview' => true,
        ]);
    }

    /**
     * Create for a specific module.
     */
    public function forModule(Module $module): static
    {
        return $this->state(fn (array $attributes) => [
            'module_id' => $module->id,
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
