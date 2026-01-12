<?php

namespace Database\Factories;

use App\Models\LearningStep;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserNote>
 */
class UserNoteFactory extends Factory
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
            'step_id' => LearningStep::factory(),
            'content' => fake()->paragraphs(2, true),
            'is_private' => true,
        ];
    }

    /**
     * Mark as private.
     */
    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_private' => true,
        ]);
    }

    /**
     * Mark as public.
     */
    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_private' => false,
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
     * Create for specific step.
     */
    public function forStep(LearningStep $step): static
    {
        return $this->state(fn (array $attributes) => [
            'step_id' => $step->id,
        ]);
    }
}
