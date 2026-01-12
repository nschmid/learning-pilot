<?php

namespace Database\Factories;

use App\Enums\AiFeedbackType;
use App\Models\AiGeneratedContent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AiFeedbackReport>
 */
class AiFeedbackReportFactory extends Factory
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
            'ai_generated_content_id' => AiGeneratedContent::factory(),
            'feedback_type' => fake()->randomElement(AiFeedbackType::cases()),
            'description' => fake()->optional()->paragraph(),
            'expected_response' => fake()->optional()->paragraph(),
            'status' => 'pending',
            'admin_notes' => null,
            'resolved_at' => null,
            'resolved_by' => null,
        ];
    }

    /**
     * Mark as reviewed.
     */
    public function reviewed(?string $notes = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'reviewed',
            'admin_notes' => $notes ?? fake()->optional()->sentence(),
        ]);
    }

    /**
     * Mark as resolved.
     */
    public function resolved(?string $notes = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolved_by' => User::factory(),
            'admin_notes' => $notes ?? fake()->optional()->sentence(),
        ]);
    }

    /**
     * Set specific feedback type.
     */
    public function type(AiFeedbackType $type): static
    {
        return $this->state(fn (array $attributes) => [
            'feedback_type' => $type,
        ]);
    }

    /**
     * Set AI generated content context.
     */
    public function forContent(AiGeneratedContent $content): static
    {
        return $this->state(fn (array $attributes) => [
            'ai_generated_content_id' => $content->id,
        ]);
    }

    /**
     * Create without content reference.
     */
    public function withoutContent(): static
    {
        return $this->state(fn (array $attributes) => [
            'ai_generated_content_id' => null,
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

    /**
     * With expected response.
     */
    public function withExpectedResponse(string $response): static
    {
        return $this->state(fn (array $attributes) => [
            'expected_response' => $response,
        ]);
    }
}
