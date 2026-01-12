<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AnswerOption>
 */
class AnswerOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question_id' => Question::factory(),
            'option_text' => fake()->sentence(),
            'is_correct' => false,
            'position' => fake()->numberBetween(1, 4),
            'feedback' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Mark as correct answer.
     */
    public function correct(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_correct' => true,
        ]);
    }

    /**
     * Mark as incorrect answer.
     */
    public function incorrect(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_correct' => false,
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
