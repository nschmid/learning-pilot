<?php

namespace Tests\Feature\Models\Core;

use App\Enums\MaterialType;
use App\Enums\VideoSourceType;
use App\Models\LearningMaterial;
use App\Models\LearningStep;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ModelTestHelpers;

class LearningMaterialTest extends TestCase
{
    use ModelTestHelpers;
    use RefreshDatabase;

    public function test_it_can_create_a_learning_material(): void
    {
        $material = $this->assertModelCanBeCreated(LearningMaterial::class);

        $this->assertNotNull($material->title);
    }

    public function test_fillable_fields_exist_in_database(): void
    {
        $material = LearningMaterial::factory()->create();

        $this->assertFillableFieldsExist($material);
    }

    public function test_uses_uuids(): void
    {
        $material = LearningMaterial::factory()->create();

        $this->assertUsesUuids($material);
    }

    public function test_enum_casts_work(): void
    {
        $material = LearningMaterial::factory()->text()->create();

        $this->assertEnumCast($material, 'material_type', MaterialType::class);
    }

    public function test_video_source_type_cast_works(): void
    {
        $material = LearningMaterial::factory()->youtube()->create();

        $this->assertEnumCast($material, 'video_source_type', VideoSourceType::class);
    }

    public function test_array_casts_work(): void
    {
        $material = LearningMaterial::factory()->create([
            'metadata' => ['key' => 'value'],
        ]);

        $this->assertArrayCast($material, 'metadata');
    }

    public function test_step_relationship_works(): void
    {
        $step = LearningStep::factory()->create();
        $material = LearningMaterial::factory()->forStep($step)->create();

        $this->assertBelongsToRelationship($material, 'step', LearningStep::class);
    }

    public function test_ordered_scope_works(): void
    {
        $step = LearningStep::factory()->create();
        LearningMaterial::factory()->forStep($step)->create(['position' => 3]);
        LearningMaterial::factory()->forStep($step)->create(['position' => 1]);

        $materials = LearningMaterial::ordered()->get();

        $this->assertEquals(1, $materials->first()->position);
    }

    public function test_by_type_scope_works(): void
    {
        LearningMaterial::factory()->count(2)->text()->create();
        LearningMaterial::factory()->count(3)->video()->create();

        $this->assertCount(2, LearningMaterial::byType(MaterialType::Text)->get());
        $this->assertCount(3, LearningMaterial::byType(MaterialType::Video)->get());
    }

    public function test_is_video_helper_works(): void
    {
        $video = LearningMaterial::factory()->video()->create();
        $text = LearningMaterial::factory()->text()->create();

        $this->assertTrue($video->isVideo());
        $this->assertFalse($text->isVideo());
    }

    public function test_get_embed_url_works_for_youtube(): void
    {
        $material = LearningMaterial::factory()->youtube()->create([
            'video_source_id' => 'dQw4w9WgXcQ',
        ]);

        $this->assertEquals('https://www.youtube.com/embed/dQw4w9WgXcQ', $material->getEmbedUrl());
    }

    public function test_get_duration_formatted_works(): void
    {
        $material = LearningMaterial::factory()->video()->create([
            'duration_seconds' => 125,
        ]);

        $this->assertEquals('2:05', $material->getDurationFormatted());
    }
}
