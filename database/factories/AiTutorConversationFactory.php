<?php

namespace Database\Factories;

use App\Models\LearningPath;
use App\Models\LearningStep;
use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AiTutorConversation>
 */
class AiTutorConversationFactory extends Factory
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
            'learning_path_id' => null,
            'module_id' => null,
            'step_id' => null,
            'title' => fake()->sentence(4),
            'status' => 'active',
            'system_context' => null,
            'total_messages' => 0,
            'total_tokens_used' => 0,
            'last_message_at' => null,
        ];
    }

    /**
     * Indicate that the conversation is archived.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'archived',
        ]);
    }

    /**
     * Indicate that the conversation is resolved.
     */
    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'resolved',
        ]);
    }

    /**
     * Set context to a learning path.
     */
    public function forPath(LearningPath $path): static
    {
        return $this->state(fn (array $attributes) => [
            'learning_path_id' => $path->id,
        ]);
    }

    /**
     * Set context to a module.
     */
    public function forModule(Module $module): static
    {
        return $this->state(fn (array $attributes) => [
            'module_id' => $module->id,
        ]);
    }

    /**
     * Set context to a step.
     */
    public function forStep(LearningStep $step): static
    {
        return $this->state(fn (array $attributes) => [
            'step_id' => $step->id,
        ]);
    }

    /**
     * Create with some messages.
     */
    public function withMessages(int $count): static
    {
        return $this->state(fn (array $attributes) => [
            'total_messages' => $count,
            'total_tokens_used' => $count * fake()->numberBetween(100, 500),
            'last_message_at' => now(),
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
