<?php

namespace Database\Factories;

use App\Models\LearningPath;
use App\Models\LearningStep;
use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AiPracticeSession>
 */
class AiPracticeSessionFactory extends Factory
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
            'difficulty' => fake()->randomElement(['beginner', 'intermediate', 'advanced', 'adaptive']),
            'question_count' => 10,
            'focus_areas' => null,
            'questions_generated' => 0,
            'questions_answered' => 0,
            'correct_answers' => 0,
            'status' => 'active',
            'started_at' => now(),
            'completed_at' => null,
        ];
    }

    /**
     * Indicate that the session is completed.
     */
    public function completed(): static
    {
        $answered = fake()->numberBetween(5, 10);
        $correct = fake()->numberBetween(0, $answered);

        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'questions_answered' => $answered,
            'correct_answers' => $correct,
            'questions_generated' => $answered,
            'completed_at' => now(),
        ]);
    }

    /**
     * Indicate that the session is abandoned.
     */
    public function abandoned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'abandoned',
        ]);
    }

    /**
     * Set difficulty.
     */
    public function difficulty(string $difficulty): static
    {
        return $this->state(fn (array $attributes) => [
            'difficulty' => $difficulty,
        ]);
    }

    /**
     * Set question count.
     */
    public function questionCount(int $count): static
    {
        return $this->state(fn (array $attributes) => [
            'question_count' => $count,
        ]);
    }

    /**
     * Set learning path context.
     */
    public function forPath(LearningPath $path): static
    {
        return $this->state(fn (array $attributes) => [
            'learning_path_id' => $path->id,
        ]);
    }

    /**
     * Set module context.
     */
    public function forModule(Module $module): static
    {
        return $this->state(fn (array $attributes) => [
            'module_id' => $module->id,
        ]);
    }

    /**
     * Set step context.
     */
    public function forStep(LearningStep $step): static
    {
        return $this->state(fn (array $attributes) => [
            'step_id' => $step->id,
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
     * Set progress.
     */
    public function withProgress(int $answered, int $correct): static
    {
        return $this->state(fn (array $attributes) => [
            'questions_answered' => $answered,
            'correct_answers' => $correct,
        ]);
    }

    /**
     * Set focus areas.
     */
    public function withFocusAreas(array $areas): static
    {
        return $this->state(fn (array $attributes) => [
            'focus_areas' => $areas,
        ]);
    }
}
