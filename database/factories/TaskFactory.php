<?php

namespace Database\Factories;

use App\Enums\TaskType;
use App\Models\LearningStep;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'step_id' => LearningStep::factory()->task(),
            'task_type' => TaskType::Submission,
            'title' => fake()->sentence(4),
            'instructions' => fake()->paragraphs(2, true),
            'max_points' => 100,
            'due_days' => fake()->numberBetween(3, 14),
            'allow_late' => true,
            'allow_resubmit' => true,
            'rubric' => [
                ['criterion' => 'Vollständigkeit', 'max_points' => 40],
                ['criterion' => 'Qualität', 'max_points' => 40],
                ['criterion' => 'Formatierung', 'max_points' => 20],
            ],
            'allowed_file_types' => ['pdf', 'doc', 'docx'],
            'max_file_size_mb' => 10,
        ];
    }

    /**
     * Create a submission task.
     */
    public function submission(): static
    {
        return $this->state(fn (array $attributes) => [
            'task_type' => TaskType::Submission,
        ]);
    }

    /**
     * Create a project task.
     */
    public function project(): static
    {
        return $this->state(fn (array $attributes) => [
            'task_type' => TaskType::Project,
            'max_points' => 200,
            'due_days' => fake()->numberBetween(14, 30),
        ]);
    }

    /**
     * Create a discussion task.
     */
    public function discussion(): static
    {
        return $this->state(fn (array $attributes) => [
            'task_type' => TaskType::Discussion,
            'max_points' => 50,
            'allowed_file_types' => null,
            'max_file_size_mb' => 0,
        ]);
    }

    /**
     * Disallow late submissions.
     */
    public function noLateSubmissions(): static
    {
        return $this->state(fn (array $attributes) => [
            'allow_late' => false,
        ]);
    }

    /**
     * Disallow resubmissions.
     */
    public function noResubmissions(): static
    {
        return $this->state(fn (array $attributes) => [
            'allow_resubmit' => false,
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
