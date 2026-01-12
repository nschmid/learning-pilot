<?php

namespace Tests\Feature\Models\Core;

use App\Models\LearningPath;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ModelTestHelpers;

class TagTest extends TestCase
{
    use ModelTestHelpers;
    use RefreshDatabase;

    public function test_it_can_create_a_tag(): void
    {
        $tag = $this->assertModelCanBeCreated(Tag::class);

        $this->assertNotNull($tag->name);
        $this->assertNotNull($tag->slug);
    }

    public function test_fillable_fields_exist_in_database(): void
    {
        $tag = Tag::factory()->create();

        $this->assertFillableFieldsExist($tag);
    }

    public function test_slug_is_auto_generated(): void
    {
        $tag = Tag::factory()->create(['name' => 'Test Tag Name']);

        $this->assertEquals('test-tag-name', $tag->slug);
    }

    public function test_learning_paths_relationship_works(): void
    {
        $tag = Tag::factory()->create();
        $paths = LearningPath::factory()->count(3)->create();

        $tag->learningPaths()->attach($paths->pluck('id'));

        $this->assertBelongsToManyRelationship($tag, 'learningPaths', LearningPath::class);
        $this->assertCount(3, $tag->learningPaths);
    }
}
