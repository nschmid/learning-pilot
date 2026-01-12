<?php

namespace Database\Factories;

use App\Enums\AiContentType;
use App\Models\LearningStep;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AiGeneratedContent>
 */
class AiGeneratedContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $contentType = fake()->randomElement(AiContentType::cases());

        return [
            'contentable_type' => LearningStep::class,
            'contentable_id' => LearningStep::factory(),
            'user_id' => User::factory(),
            'content_type' => $contentType,
            'content' => fake()->paragraphs(3, true),
            'content_metadata' => [
                'model' => 'claude-sonnet-4-5-20250929',
                'tokens_input' => fake()->numberBetween(100, 500),
                'tokens_output' => fake()->numberBetween(200, 1000),
                'latency_ms' => fake()->numberBetween(500, 3000),
            ],
            'context_snapshot' => [
                'step_title' => fake()->sentence(3),
                'module_title' => fake()->sentence(2),
            ],
            'rating' => null,
            'was_helpful' => null,
            'user_feedback' => null,
            'cache_key' => 'content:'.fake()->uuid(),
            'expires_at' => now()->addDays($contentType->cacheDuration() / 60 / 24),
            'version' => 1,
        ];
    }

    /**
     * Create explanation content.
     */
    public function explanation(): static
    {
        return $this->state(fn (array $attributes) => [
            'content_type' => AiContentType::Explanation,
            'cache_key' => 'explanation:'.fake()->uuid(),
        ]);
    }

    /**
     * Create hint content.
     */
    public function hint(int $level = 1): static
    {
        return $this->state(fn (array $attributes) => [
            'content_type' => AiContentType::Hint,
            'cache_key' => 'hint:'.fake()->uuid().':level:'.$level,
            'content_metadata' => array_merge($attributes['content_metadata'] ?? [], [
                'hint_level' => $level,
            ]),
        ]);
    }

    /**
     * Create summary content.
     */
    public function summary(): static
    {
        return $this->state(fn (array $attributes) => [
            'content_type' => AiContentType::Summary,
            'cache_key' => 'summary:'.fake()->uuid(),
        ]);
    }

    /**
     * Create flashcard content.
     */
    public function flashcard(): static
    {
        $cards = [];
        for ($i = 0; $i < 10; $i++) {
            $cards[] = [
                'front' => fake()->sentence().'?',
                'back' => fake()->sentence(),
                'hint' => fake()->optional()->sentence(),
            ];
        }

        return $this->state(fn (array $attributes) => [
            'content_type' => AiContentType::Flashcard,
            'content' => json_encode($cards),
            'cache_key' => 'flashcards:'.fake()->uuid(),
        ]);
    }

    /**
     * Mark as expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDay(),
        ]);
    }

    /**
     * Mark as never expiring.
     */
    public function neverExpires(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => null,
        ]);
    }

    /**
     * Mark as helpful.
     */
    public function helpful(): static
    {
        return $this->state(fn (array $attributes) => [
            'was_helpful' => true,
            'rating' => fake()->numberBetween(4, 5),
        ]);
    }

    /**
     * Mark as not helpful.
     */
    public function notHelpful(): static
    {
        return $this->state(fn (array $attributes) => [
            'was_helpful' => false,
            'rating' => fake()->numberBetween(1, 2),
            'user_feedback' => fake()->sentence(),
        ]);
    }

    /**
     * Set contentable context.
     */
    public function forContentable(Model $contentable): static
    {
        return $this->state(fn (array $attributes) => [
            'contentable_type' => get_class($contentable),
            'contentable_id' => $contentable->getKey(),
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
