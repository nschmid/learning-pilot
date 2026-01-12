<?php

namespace Database\Factories;

use App\Models\AiTutorConversation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AiTutorMessage>
 */
class AiTutorMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'conversation_id' => AiTutorConversation::factory(),
            'role' => fake()->randomElement(['user', 'assistant']),
            'content' => fake()->paragraphs(2, true),
            'model' => 'claude-sonnet-4-5-20250929',
            'tokens_input' => fake()->numberBetween(50, 200),
            'tokens_output' => fake()->numberBetween(100, 800),
            'latency_ms' => fake()->numberBetween(500, 3000),
            'references' => null,
            'created_at' => now(),
        ];
    }

    /**
     * Create a user message.
     */
    public function fromUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'user',
            'model' => null,
            'tokens_input' => null,
            'tokens_output' => null,
            'latency_ms' => null,
        ]);
    }

    /**
     * Create an assistant message.
     */
    public function fromAssistant(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'assistant',
            'tokens_input' => fake()->numberBetween(50, 300),
            'tokens_output' => fake()->numberBetween(200, 1000),
        ]);
    }

    /**
     * Create a system message.
     */
    public function fromSystem(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'system',
            'model' => null,
            'tokens_input' => null,
            'tokens_output' => null,
            'latency_ms' => null,
        ]);
    }

    /**
     * Create for specific conversation.
     */
    public function forConversation(AiTutorConversation $conversation): static
    {
        return $this->state(fn (array $attributes) => [
            'conversation_id' => $conversation->id,
        ]);
    }
}
