<?php

namespace Database\Factories;

use App\Enums\QuestionType;
use App\Models\AiPracticeSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AiPracticeQuestion>
 */
class AiPracticeQuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $position = 0;

        return [
            'session_id' => AiPracticeSession::factory(),
            'question_type' => QuestionType::SingleChoice,
            'question_text' => fake()->sentence().'?',
            'options' => [
                'A' => fake()->sentence(),
                'B' => fake()->sentence(),
                'C' => fake()->sentence(),
                'D' => fake()->sentence(),
            ],
            'correct_answer' => fake()->randomElement(['A', 'B', 'C', 'D']),
            'explanation' => fake()->paragraph(),
            'difficulty' => fake()->randomElement(['beginner', 'intermediate', 'advanced', 'expert']),
            'topics' => null,
            'source_material_ids' => null,
            'user_answer' => null,
            'is_correct' => null,
            'answered_at' => null,
            'time_spent_seconds' => null,
            'ai_feedback' => null,
            'position' => ++$position,
            'created_at' => now(),
        ];
    }

    /**
     * Mark as answered correctly.
     */
    public function answeredCorrectly(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'user_answer' => $attributes['correct_answer'],
                'is_correct' => true,
                'answered_at' => now(),
                'time_spent_seconds' => fake()->numberBetween(10, 120),
            ];
        });
    }

    /**
     * Mark as answered incorrectly.
     */
    public function answeredIncorrectly(): static
    {
        return $this->state(function (array $attributes) {
            $options = array_keys($attributes['options'] ?? ['A', 'B', 'C', 'D']);
            $wrong = array_diff($options, [$attributes['correct_answer']]);

            return [
                'user_answer' => fake()->randomElement($wrong),
                'is_correct' => false,
                'answered_at' => now(),
                'time_spent_seconds' => fake()->numberBetween(10, 120),
            ];
        });
    }

    /**
     * Set question type to true/false.
     */
    public function trueFalse(): static
    {
        return $this->state(fn (array $attributes) => [
            'question_type' => QuestionType::TrueFalse,
            'options' => ['True' => 'Wahr', 'False' => 'Falsch'],
            'correct_answer' => fake()->randomElement(['True', 'False']),
        ]);
    }

    /**
     * Set question type to multiple choice.
     */
    public function multipleChoice(): static
    {
        return $this->state(fn (array $attributes) => [
            'question_type' => QuestionType::MultipleChoice,
            'correct_answer' => 'A,B',
        ]);
    }

    /**
     * Create for specific session.
     */
    public function forSession(AiPracticeSession $session): static
    {
        return $this->state(fn (array $attributes) => [
            'session_id' => $session->id,
        ]);
    }

    /**
     * Set difficulty.
     */
    public function difficulty(string $level): static
    {
        return $this->state(fn (array $attributes) => [
            'difficulty' => $level,
        ]);
    }

    /**
     * Set topics.
     */
    public function withTopics(array $topics): static
    {
        return $this->state(fn (array $attributes) => [
            'topics' => $topics,
        ]);
    }

    /**
     * Set position.
     */
    public function atPosition(int $position): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => $position,
        ]);
    }

    /**
     * With AI feedback.
     */
    public function withFeedback(string $feedback): static
    {
        return $this->state(fn (array $attributes) => [
            'ai_feedback' => $feedback,
        ]);
    }
}
