<?php

namespace Database\Factories;

use App\Enums\QuestionType;
use App\Models\Assessment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
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
            'question_type' => QuestionType::SingleChoice,
            'question_text' => fake()->sentence().'?',
            'question_image' => null,
            'explanation' => fake()->paragraph(),
            'points' => fake()->randomElement([5, 10, 15, 20]),
            'position' => fake()->numberBetween(1, 20),
            'metadata' => [],
        ];
    }

    /**
     * Create a single choice question.
     */
    public function singleChoice(): static
    {
        return $this->state(fn (array $attributes) => [
            'question_type' => QuestionType::SingleChoice,
        ]);
    }

    /**
     * Create a multiple choice question.
     */
    public function multipleChoice(): static
    {
        return $this->state(fn (array $attributes) => [
            'question_type' => QuestionType::MultipleChoice,
        ]);
    }

    /**
     * Create a true/false question.
     */
    public function trueFalse(): static
    {
        return $this->state(fn (array $attributes) => [
            'question_type' => QuestionType::TrueFalse,
        ]);
    }

    /**
     * Create a text question.
     */
    public function text(): static
    {
        return $this->state(fn (array $attributes) => [
            'question_type' => QuestionType::Text,
        ]);
    }

    /**
     * Create a matching question.
     */
    public function matching(): static
    {
        return $this->state(fn (array $attributes) => [
            'question_type' => QuestionType::Matching,
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
     * Set position.
     */
    public function position(int $position): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => $position,
        ]);
    }
}
