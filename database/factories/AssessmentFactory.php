<?php

namespace Database\Factories;

use App\Enums\AssessmentType;
use App\Models\LearningStep;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assessment>
 */
class AssessmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'step_id' => LearningStep::factory()->assessment(),
            'assessment_type' => AssessmentType::Quiz,
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'instructions' => fake()->paragraph(),
            'time_limit_minutes' => null,
            'passing_score_percent' => 70,
            'max_attempts' => 3,
            'shuffle_questions' => true,
            'shuffle_answers' => true,
            'show_correct_answers' => true,
            'show_score_immediately' => true,
        ];
    }

    /**
     * Create a quiz.
     */
    public function quiz(): static
    {
        return $this->state(fn (array $attributes) => [
            'assessment_type' => AssessmentType::Quiz,
            'time_limit_minutes' => null,
            'max_attempts' => 3,
        ]);
    }

    /**
     * Create an exam.
     */
    public function exam(): static
    {
        return $this->state(fn (array $attributes) => [
            'assessment_type' => AssessmentType::Exam,
            'time_limit_minutes' => fake()->randomElement([30, 60, 90, 120]),
            'max_attempts' => 1,
            'passing_score_percent' => 60,
        ]);
    }

    /**
     * Create a survey.
     */
    public function survey(): static
    {
        return $this->state(fn (array $attributes) => [
            'assessment_type' => AssessmentType::Survey,
            'passing_score_percent' => 0,
            'max_attempts' => 1,
            'show_correct_answers' => false,
        ]);
    }

    /**
     * Set a time limit.
     */
    public function withTimeLimit(int $minutes): static
    {
        return $this->state(fn (array $attributes) => [
            'time_limit_minutes' => $minutes,
        ]);
    }

    /**
     * Allow unlimited attempts.
     */
    public function unlimitedAttempts(): static
    {
        return $this->state(fn (array $attributes) => [
            'max_attempts' => null,
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
