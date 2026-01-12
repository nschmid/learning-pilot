<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AiUserQuota>
 */
class AiUserQuotaFactory extends Factory
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
            'monthly_token_limit' => 100000,
            'daily_request_limit' => 100,
            'tokens_used_this_month' => 0,
            'requests_today' => 0,
            'last_request_at' => null,
            'month_reset_at' => now()->startOfMonth(),
            'feature_explanations_enabled' => true,
            'feature_tutor_enabled' => true,
            'feature_practice_enabled' => true,
            'feature_summaries_enabled' => true,
        ];
    }

    /**
     * Indicate that all tokens are used.
     */
    public function exhaustedTokens(): static
    {
        return $this->state(fn (array $attributes) => [
            'tokens_used_this_month' => $attributes['monthly_token_limit'],
        ]);
    }

    /**
     * Indicate that daily requests are exhausted.
     */
    public function exhaustedRequests(): static
    {
        return $this->state(fn (array $attributes) => [
            'requests_today' => $attributes['daily_request_limit'],
        ]);
    }

    /**
     * Create quota with specific usage.
     */
    public function withUsage(int $tokensUsed, int $requestsMade): static
    {
        return $this->state(fn (array $attributes) => [
            'tokens_used_this_month' => $tokensUsed,
            'requests_today' => $requestsMade,
            'last_request_at' => now(),
        ]);
    }

    /**
     * Disable all AI features.
     */
    public function allFeaturesDisabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'feature_explanations_enabled' => false,
            'feature_tutor_enabled' => false,
            'feature_practice_enabled' => false,
            'feature_summaries_enabled' => false,
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
