<?php

namespace Database\Factories;

use App\Enums\SubmissionStatus;
use App\Models\Enrollment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskSubmission>
 */
class TaskSubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'task_id' => Task::factory(),
            'enrollment_id' => Enrollment::factory(),
            'content' => fake()->paragraphs(3, true),
            'file_paths' => [],
            'status' => SubmissionStatus::Pending,
            'score' => null,
            'feedback' => null,
            'submitted_at' => now(),
            'reviewed_at' => null,
            'reviewer_id' => null,
        ];
    }

    /**
     * Mark as pending review.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SubmissionStatus::Pending,
            'score' => null,
            'feedback' => null,
            'reviewed_at' => null,
            'reviewer_id' => null,
        ]);
    }

    /**
     * Mark as reviewed.
     */
    public function reviewed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SubmissionStatus::Reviewed,
            'score' => fake()->numberBetween(60, 100),
            'feedback' => fake()->paragraph(),
            'reviewed_at' => now(),
            'reviewer_id' => User::factory(),
        ]);
    }

    /**
     * Mark as revision requested.
     */
    public function revisionRequested(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SubmissionStatus::RevisionRequested,
            'score' => null,
            'feedback' => fake()->paragraph(),
            'reviewed_at' => now(),
            'reviewer_id' => User::factory(),
        ]);
    }

    /**
     * Include file paths.
     */
    public function withFiles(array $files = ['submissions/file1.pdf', 'submissions/file2.pdf']): static
    {
        return $this->state(fn (array $attributes) => [
            'file_paths' => $files,
        ]);
    }

    /**
     * Create for specific task.
     */
    public function forTask(Task $task): static
    {
        return $this->state(fn (array $attributes) => [
            'task_id' => $task->id,
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
