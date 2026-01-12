<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AssessmentAttempt>
 */
class AssessmentAttemptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'assessment_id' => Assessment::factory(),
            'enrollment_id' => Enrollment::factory(),
            'attempt_number' => 1,
            'started_at' => now(),
            'completed_at' => null,
            'score_percent' => null,
            'points_earned' => 0,
            'passed' => false,
            'time_spent_seconds' => 0,
            'answers' => [],
        ];
    }

    /**
     * Mark as in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'started_at' => now()->subMinutes(fake()->numberBetween(5, 30)),
            'completed_at' => null,
        ]);
    }

    /**
     * Mark as completed.
     */
    public function completed(): static
    {
        $score = fake()->numberBetween(50, 100);

        return $this->state(fn (array $attributes) => [
            'completed_at' => now(),
            'score_percent' => $score,
            'points_earned' => fake()->numberBetween(50, 100),
            'passed' => $score >= 70,
            'time_spent_seconds' => fake()->numberBetween(300, 3600),
        ]);
    }

    /**
     * Mark as passed.
     */
    public function passed(): static
    {
        return $this->state(fn (array $attributes) => [
            'completed_at' => now(),
            'score_percent' => fake()->numberBetween(70, 100),
            'points_earned' => fake()->numberBetween(70, 100),
            'passed' => true,
            'time_spent_seconds' => fake()->numberBetween(300, 3600),
        ]);
    }

    /**
     * Mark as failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'completed_at' => now(),
            'score_percent' => fake()->numberBetween(0, 69),
            'points_earned' => fake()->numberBetween(0, 69),
            'passed' => false,
            'time_spent_seconds' => fake()->numberBetween(300, 3600),
        ]);
    }

    /**
     * Set attempt number.
     */
    public function attemptNumber(int $number): static
    {
        return $this->state(fn (array $attributes) => [
            'attempt_number' => $number,
        ]);
    }

    /**
     * Create for specific assessment.
     */
    public function forAssessment(Assessment $assessment): static
    {
        return $this->state(fn (array $attributes) => [
            'assessment_id' => $assessment->id,
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
}
