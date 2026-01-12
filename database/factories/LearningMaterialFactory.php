<?php

namespace Database\Factories;

use App\Enums\MaterialType;
use App\Enums\VideoSourceType;
use App\Models\LearningStep;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LearningMaterial>
 */
class LearningMaterialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'step_id' => LearningStep::factory(),
            'material_type' => MaterialType::Text,
            'title' => fake()->sentence(3),
            'content' => fake()->paragraphs(3, true),
            'file_path' => null,
            'file_name' => null,
            'mime_type' => null,
            'file_size' => null,
            'duration_seconds' => null,
            'external_url' => null,
            'video_source_type' => null,
            'video_source_id' => null,
            'position' => fake()->numberBetween(1, 10),
            'metadata' => [],
        ];
    }

    /**
     * Create a text material.
     */
    public function text(): static
    {
        return $this->state(fn (array $attributes) => [
            'material_type' => MaterialType::Text,
            'content' => fake()->paragraphs(5, true),
        ]);
    }

    /**
     * Create a video material (uploaded).
     */
    public function video(): static
    {
        return $this->state(fn (array $attributes) => [
            'material_type' => MaterialType::Video,
            'video_source_type' => VideoSourceType::Upload,
            'file_path' => 'materials/videos/sample.mp4',
            'file_name' => 'sample.mp4',
            'mime_type' => 'video/mp4',
            'file_size' => fake()->numberBetween(1000000, 100000000),
            'duration_seconds' => fake()->numberBetween(60, 1800),
        ]);
    }

    /**
     * Create a YouTube video material.
     */
    public function youtube(): static
    {
        return $this->state(fn (array $attributes) => [
            'material_type' => MaterialType::Video,
            'video_source_type' => VideoSourceType::YouTube,
            'video_source_id' => fake()->regexify('[a-zA-Z0-9_-]{11}'),
            'external_url' => 'https://www.youtube.com/watch?v='.fake()->regexify('[a-zA-Z0-9_-]{11}'),
            'duration_seconds' => fake()->numberBetween(60, 1800),
        ]);
    }

    /**
     * Create a PDF material.
     */
    public function pdf(): static
    {
        return $this->state(fn (array $attributes) => [
            'material_type' => MaterialType::Pdf,
            'file_path' => 'materials/pdfs/document.pdf',
            'file_name' => 'document.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => fake()->numberBetween(10000, 5000000),
        ]);
    }

    /**
     * Create an audio material.
     */
    public function audio(): static
    {
        return $this->state(fn (array $attributes) => [
            'material_type' => MaterialType::Audio,
            'file_path' => 'materials/audio/recording.mp3',
            'file_name' => 'recording.mp3',
            'mime_type' => 'audio/mpeg',
            'file_size' => fake()->numberBetween(1000000, 50000000),
            'duration_seconds' => fake()->numberBetween(60, 3600),
        ]);
    }

    /**
     * Create a link material.
     */
    public function link(): static
    {
        return $this->state(fn (array $attributes) => [
            'material_type' => MaterialType::Link,
            'external_url' => fake()->url(),
        ]);
    }

    /**
     * Create for specific step.
     */
    public function forStep(LearningStep $step): static
    {
        return $this->state(fn (array $attributes) => [
            'step_id' => $step->id,
        ]);
    }
}
