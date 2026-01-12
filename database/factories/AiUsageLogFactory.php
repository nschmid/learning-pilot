<?php

namespace Database\Factories;

use App\Enums\AiServiceType;
use App\Models\LearningStep;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AiUsageLog>
 */
class AiUsageLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tokensInput = fake()->numberBetween(100, 1000);
        $tokensOutput = fake()->numberBetween(200, 2000);

        return [
            'user_id' => User::factory(),
            'service_type' => fake()->randomElement(AiServiceType::cases()),
            'model' => 'claude-sonnet-4-5-20250929',
            'tokens_input' => $tokensInput,
            'tokens_output' => $tokensOutput,
            'tokens_total' => $tokensInput + $tokensOutput,
            'cost_credits' => fake()->optional()->randomFloat(4, 0.0001, 0.1),
            'latency_ms' => fake()->numberBetween(500, 5000),
            'cache_hit' => false,
            'context_type' => LearningStep::class,
            'context_id' => LearningStep::factory(),
            'created_at' => now(),
        ];
    }

    /**
     * Mark as cached.
     */
    public function cached(): static
    {
        return $this->state(fn (array $attributes) => [
            'cache_hit' => true,
            'tokens_input' => 0,
            'tokens_output' => 0,
            'tokens_total' => 0,
            'latency_ms' => fake()->numberBetween(1, 10),
        ]);
    }

    /**
     * Set specific service type.
     */
    public function serviceType(AiServiceType $type): static
    {
        return $this->state(fn (array $attributes) => [
            'service_type' => $type,
        ]);
    }

    /**
     * Set context for the log.
     */
    public function forContext(Model $context): static
    {
        return $this->state(fn (array $attributes) => [
            'context_type' => get_class($context),
            'context_id' => $context->getKey(),
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
}
