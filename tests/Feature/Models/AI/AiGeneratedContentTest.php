<?php

namespace Tests\Feature\Models\AI;

use App\Enums\AiContentType;
use App\Models\AiGeneratedContent;
use App\Models\LearningStep;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ModelTestHelpers;

class AiGeneratedContentTest extends TestCase
{
    use ModelTestHelpers;
    use RefreshDatabase;

    public function test_it_can_create_an_ai_generated_content(): void
    {
        $content = $this->assertModelCanBeCreated(AiGeneratedContent::class);

        $this->assertNotNull($content->content);
    }

    public function test_fillable_fields_exist_in_database(): void
    {
        $content = AiGeneratedContent::factory()->create();

        $this->assertFillableFieldsExist($content);
    }

    public function test_uses_uuids(): void
    {
        $content = AiGeneratedContent::factory()->create();

        $this->assertUsesUuids($content);
    }

    public function test_datetime_casts_work(): void
    {
        $content = AiGeneratedContent::factory()->create([
            'expires_at' => now()->addDays(7),
        ]);

        $this->assertDatetimeCast($content, 'expires_at');
    }

    public function test_boolean_casts_work(): void
    {
        $content = AiGeneratedContent::factory()->helpful()->create();

        $this->assertBooleanCast($content, 'was_helpful');
    }

    public function test_enum_casts_work(): void
    {
        $content = AiGeneratedContent::factory()->create([
            'content_type' => AiContentType::Explanation,
        ]);

        $this->assertEnumCast($content, 'content_type', AiContentType::class);
    }

    public function test_array_casts_work(): void
    {
        $content = AiGeneratedContent::factory()->create([
            'content_metadata' => ['tokens' => 500],
            'context_snapshot' => ['step' => 'Test Step'],
        ]);

        $this->assertArrayCast($content, 'content_metadata');
        $this->assertArrayCast($content, 'context_snapshot');
    }

    public function test_user_relationship_works(): void
    {
        $user = User::factory()->create();
        $content = AiGeneratedContent::factory()->forUser($user)->create();

        $this->assertBelongsToRelationship($content, 'user', User::class);
    }

    public function test_contentable_polymorphic_relationship_works(): void
    {
        $step = LearningStep::factory()->create();
        $content = AiGeneratedContent::factory()->forContentable($step)->create();

        $this->assertInstanceOf(LearningStep::class, $content->contentable);
        $this->assertEquals($step->id, $content->contentable->id);
    }

    public function test_contentable_works_with_different_model_types(): void
    {
        $module = Module::factory()->create();
        $content = AiGeneratedContent::factory()->forContentable($module)->create();

        $this->assertInstanceOf(Module::class, $content->contentable);
        $this->assertEquals($module->id, $content->contentable->id);
    }

    public function test_cached_scope_works(): void
    {
        AiGeneratedContent::factory()->count(3)->create(['expires_at' => now()->addDays(7)]);
        AiGeneratedContent::factory()->count(2)->expired()->create();
        AiGeneratedContent::factory()->count(1)->neverExpires()->create();

        $this->assertCount(4, AiGeneratedContent::cached()->get());
    }

    public function test_expired_scope_works(): void
    {
        AiGeneratedContent::factory()->count(2)->create(['expires_at' => now()->addDays(7)]);
        AiGeneratedContent::factory()->count(3)->expired()->create();

        $this->assertCount(3, AiGeneratedContent::expired()->get());
    }

    public function test_of_type_scope_works(): void
    {
        AiGeneratedContent::factory()->count(3)->explanation()->create();
        AiGeneratedContent::factory()->count(2)->summary()->create();

        $this->assertCount(3, AiGeneratedContent::ofType(AiContentType::Explanation)->get());
    }

    public function test_is_cache_valid_helper_works(): void
    {
        $valid = AiGeneratedContent::factory()->create(['expires_at' => now()->addDay()]);
        $expired = AiGeneratedContent::factory()->expired()->create();
        $neverExpires = AiGeneratedContent::factory()->neverExpires()->create();

        $this->assertTrue($valid->isCacheValid());
        $this->assertFalse($expired->isCacheValid());
        $this->assertTrue($neverExpires->isCacheValid());
    }

    public function test_mark_as_expired_helper_works(): void
    {
        $content = AiGeneratedContent::factory()->create([
            'expires_at' => now()->addDays(7),
        ]);

        $content->markAsExpired();

        $this->assertTrue($content->fresh()->expires_at->isPast());
    }

    public function test_explanation_factory_state_works(): void
    {
        $content = AiGeneratedContent::factory()->explanation()->create();

        $this->assertEquals(AiContentType::Explanation, $content->content_type);
        $this->assertStringContainsString('explanation:', $content->cache_key);
    }

    public function test_hint_factory_state_works(): void
    {
        $content = AiGeneratedContent::factory()->hint(2)->create();

        $this->assertEquals(AiContentType::Hint, $content->content_type);
        $this->assertStringContainsString('hint:', $content->cache_key);
        $this->assertEquals(2, $content->content_metadata['hint_level']);
    }

    public function test_summary_factory_state_works(): void
    {
        $content = AiGeneratedContent::factory()->summary()->create();

        $this->assertEquals(AiContentType::Summary, $content->content_type);
        $this->assertStringContainsString('summary:', $content->cache_key);
    }

    public function test_flashcard_factory_state_works(): void
    {
        $content = AiGeneratedContent::factory()->flashcard()->create();

        $this->assertEquals(AiContentType::Flashcard, $content->content_type);
        $this->assertStringContainsString('flashcards:', $content->cache_key);

        $cards = json_decode($content->content, true);
        $this->assertIsArray($cards);
        $this->assertCount(10, $cards);
        $this->assertArrayHasKey('front', $cards[0]);
        $this->assertArrayHasKey('back', $cards[0]);
    }

    public function test_expired_factory_state_works(): void
    {
        $content = AiGeneratedContent::factory()->expired()->create();

        $this->assertTrue($content->expires_at->isPast());
    }

    public function test_never_expires_factory_state_works(): void
    {
        $content = AiGeneratedContent::factory()->neverExpires()->create();

        $this->assertNull($content->expires_at);
    }

    public function test_helpful_factory_state_works(): void
    {
        $content = AiGeneratedContent::factory()->helpful()->create();

        $this->assertTrue($content->was_helpful);
        $this->assertGreaterThanOrEqual(4, $content->rating);
    }

    public function test_not_helpful_factory_state_works(): void
    {
        $content = AiGeneratedContent::factory()->notHelpful()->create();

        $this->assertFalse($content->was_helpful);
        $this->assertLessThanOrEqual(2, $content->rating);
        $this->assertNotNull($content->user_feedback);
    }
}
