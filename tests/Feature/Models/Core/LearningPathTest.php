<?php

namespace Tests\Feature\Models\Core;

use App\Enums\Difficulty;
use App\Models\Category;
use App\Models\Enrollment;
use App\Models\LearningPath;
use App\Models\Module;
use App\Models\PathReview;
use App\Models\Tag;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ModelTestHelpers;

class LearningPathTest extends TestCase
{
    use ModelTestHelpers;
    use RefreshDatabase;

    public function test_it_can_create_a_learning_path(): void
    {
        $path = $this->assertModelCanBeCreated(LearningPath::class);

        $this->assertNotNull($path->title);
        $this->assertNotNull($path->slug);
    }

    public function test_fillable_fields_exist_in_database(): void
    {
        $path = LearningPath::factory()->create();

        $this->assertFillableFieldsExist($path);
    }

    public function test_uses_uuids(): void
    {
        $path = LearningPath::factory()->create();

        $this->assertUsesUuids($path);
    }

    public function test_soft_deletes_work(): void
    {
        $path = LearningPath::factory()->create();

        $this->assertSoftDeletes($path);
    }

    public function test_enum_casts_work(): void
    {
        $path = LearningPath::factory()->create(['difficulty' => Difficulty::Intermediate]);

        $this->assertEnumCast($path, 'difficulty', Difficulty::class);
        $this->assertEquals(Difficulty::Intermediate, $path->difficulty);
    }

    public function test_boolean_casts_work(): void
    {
        $path = LearningPath::factory()->published()->featured()->create();

        $this->assertBooleanCast($path, 'is_published');
        $this->assertBooleanCast($path, 'is_featured');
    }

    public function test_array_casts_work(): void
    {
        $path = LearningPath::factory()->create([
            'objectives' => ['Objective 1', 'Objective 2'],
            'metadata' => ['key' => 'value'],
        ]);

        $this->assertArrayCast($path, 'objectives');
        $this->assertArrayCast($path, 'metadata');
    }

    public function test_datetime_casts_work(): void
    {
        $path = LearningPath::factory()->published()->create();

        $this->assertDatetimeCast($path, 'published_at');
    }

    public function test_creator_relationship_works(): void
    {
        $user = User::factory()->create();
        $path = LearningPath::factory()->createdBy($user)->create();

        $this->assertBelongsToRelationship($path, 'creator', User::class);
        $this->assertEquals($user->id, $path->creator_id);
    }

    public function test_team_relationship_works(): void
    {
        $team = Team::factory()->create();
        $path = LearningPath::factory()->forTeam($team)->create();

        $this->assertBelongsToRelationship($path, 'team', Team::class);
        $this->assertEquals($team->id, $path->team_id);
    }

    public function test_category_relationship_works(): void
    {
        $category = Category::factory()->create();
        $path = LearningPath::factory()->create(['category_id' => $category->id]);

        $this->assertBelongsToRelationship($path, 'category', Category::class);
    }

    public function test_tags_relationship_works(): void
    {
        $path = LearningPath::factory()->create();
        $tags = Tag::factory()->count(3)->create();
        $path->tags()->attach($tags->pluck('id'));

        $this->assertBelongsToManyRelationship($path, 'tags', Tag::class);
        $this->assertCount(3, $path->tags);
    }

    public function test_modules_relationship_works(): void
    {
        $path = LearningPath::factory()->create();
        Module::factory()->count(3)->forPath($path)->create();

        $this->assertHasManyRelationship($path, 'modules', Module::class);
        $this->assertCount(3, $path->modules);
    }

    public function test_enrollments_relationship_works(): void
    {
        $path = LearningPath::factory()->create();
        Enrollment::factory()->count(2)->forPath($path)->create();

        $this->assertHasManyRelationship($path, 'enrollments', Enrollment::class);
        $this->assertCount(2, $path->enrollments);
    }

    public function test_reviews_relationship_works(): void
    {
        $path = LearningPath::factory()->create();
        PathReview::factory()->count(2)->forPath($path)->create();

        $this->assertHasManyRelationship($path, 'reviews', PathReview::class);
    }

    public function test_published_scope_filters_correctly(): void
    {
        LearningPath::factory()->count(3)->published()->create();
        LearningPath::factory()->count(2)->create(['is_published' => false]);

        $this->assertCount(3, LearningPath::published()->get());
    }

    public function test_featured_scope_filters_correctly(): void
    {
        LearningPath::factory()->count(2)->featured()->create();
        LearningPath::factory()->count(3)->create(['is_featured' => false]);

        $this->assertCount(2, LearningPath::featured()->get());
    }

    public function test_by_difficulty_scope_works(): void
    {
        LearningPath::factory()->count(2)->difficulty(Difficulty::Beginner)->create();
        LearningPath::factory()->count(3)->difficulty(Difficulty::Advanced)->create();

        $this->assertCount(2, LearningPath::byDifficulty(Difficulty::Beginner)->get());
        $this->assertCount(3, LearningPath::byDifficulty(Difficulty::Advanced)->get());
    }

    public function test_is_published_helper_works(): void
    {
        $published = LearningPath::factory()->published()->create();
        $unpublished = LearningPath::factory()->create(['is_published' => false]);

        $this->assertTrue($published->isPublished());
        $this->assertFalse($unpublished->isPublished());
    }
}
