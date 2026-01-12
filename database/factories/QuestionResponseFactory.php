<?php

namespace Database\Factories;

use App\Models\AssessmentAttempt;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuestionResponse>
 */
class QuestionResponseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'attempt_id' => AssessmentAttempt::factory(),
            'question_id' => Question::factory(),
            'user_answer' => fake()->sentence(),
            'is_correct' => fake()->boolean(),
            'points_earned' => fake()->numberBetween(0, 10),
        ];
    }

    /**
     * Mark as correct.
     */
    public function correct(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_correct' => true,
        ]);
    }

    /**
     * Mark as incorrect.
     */
    public function incorrect(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_correct' => false,
            'points_earned' => 0,
        ]);
    }

    /**
     * Create for specific attempt.
     */
    public function forAttempt(AssessmentAttempt $attempt): static
    {
        return $this->state(fn (array $attributes) => [
            'attempt_id' => $attempt->id,
        ]);
    }

    /**
     * Create for specific question.
     */
    public function forQuestion(Question $question): static
    {
        return $this->state(fn (array $attributes) => [
            'question_id' => $question->id,
        ]);
    }
}
