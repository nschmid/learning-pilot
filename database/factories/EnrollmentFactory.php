<?php

namespace Database\Factories;

use App\Enums\EnrollmentStatus;
use App\Models\LearningPath;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enrollment>
 */
class EnrollmentFactory extends Factory
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
            'status' => EnrollmentStatus::Active,
            'progress_percent' => 0,
            'started_at' => now(),
            'completed_at' => null,
            'last_activity_at' => now(),
            'total_time_spent_seconds' => 0,
            'points_earned' => 0,
            'expires_at' => null,
        ];
    }

    /**
     * Indicate that the enrollment is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EnrollmentStatus::Completed,
            'progress_percent' => 100,
            'completed_at' => now(),
        ]);
    }

    /**
     * Indicate that the enrollment is paused.
     */
    public function paused(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EnrollmentStatus::Paused,
        ]);
    }

    /**
     * Indicate that the enrollment is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EnrollmentStatus::Expired,
            'expires_at' => now()->subDay(),
        ]);
    }

    /**
     * Set progress.
     */
    public function withProgress(int $percent): static
    {
        return $this->state(fn (array $attributes) => [
            'progress_percent' => $percent,
        ]);
    }

    /**
     * Set time spent.
     */
    public function withTimeSpent(int $seconds): static
    {
        return $this->state(fn (array $attributes) => [
            'total_time_spent_seconds' => $seconds,
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
