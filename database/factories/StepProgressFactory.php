<?php

namespace Database\Factories;

use App\Enums\StepProgressStatus;
use App\Models\Enrollment;
use App\Models\LearningStep;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StepProgress>
 */
class StepProgressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'enrollment_id' => Enrollment::factory(),
            'step_id' => LearningStep::factory(),
            'status' => StepProgressStatus::NotStarted,
            'started_at' => null,
            'completed_at' => null,
            'time_spent_seconds' => 0,
            'points_earned' => 0,
            'attempts' => 0,
            'data' => [],
        ];
    }

    /**
     * Indicate that the step is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StepProgressStatus::InProgress,
            'started_at' => now()->subMinutes(fake()->numberBetween(5, 60)),
        ]);
    }

    /**
     * Indicate that the step is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StepProgressStatus::Completed,
            'started_at' => now()->subHours(fake()->numberBetween(1, 24)),
            'completed_at' => now(),
            'time_spent_seconds' => fake()->numberBetween(60, 3600),
            'points_earned' => fake()->randomElement([10, 20, 30, 50]),
        ]);
    }

    /**
     * Indicate that the step is skipped.
     */
    public function skipped(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StepProgressStatus::Skipped,
        ]);
    }

    /**
     * Set time spent.
     */
    public function withTimeSpent(int $seconds): static
    {
        return $this->state(fn (array $attributes) => [
            'time_spent_seconds' => $seconds,
        ]);
    }

    /**
     * Set attempts.
     */
    public function withAttempts(int $attempts): static
    {
        return $this->state(fn (array $attributes) => [
            'attempts' => $attempts,
        ]);
    }

    /**
     * Create for specific enrollment.
     */
    public function forEnrollment(Enrollment $enrollment): static
    {
        return $this->state(fn (array $attributes) => [
            'enrollment_id' => $enrollment->id,
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
