<?php

namespace Tests\Feature\Models\AI;

use App\Models\AiTutorConversation;
use App\Models\AiTutorMessage;
use App\Models\LearningPath;
use App\Models\LearningStep;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ModelTestHelpers;

/**
 * NOTE: AiTutorConversation model has some mismatches with the database migration.
 *
 * Model expects:
 * - contextable_type, contextable_id (polymorphic)
 * - is_active (boolean)
 * - message_count
 * - metadata
 *
 * Database has:
 * - learning_path_id, module_id, step_id (separate foreign keys)
 * - status (string: active, archived, resolved)
 * - total_messages (not message_count)
 * - system_context (not metadata)
 *
 * Tests below use database schema. Model should be updated to match.
 *
 * @see app/Models/AiTutorConversation.php
 * @see database/migrations/2026_01_10_120302_create_ai_tutor_conversations_table.php
 */
class AiTutorConversationTest extends TestCase
{
    use ModelTestHelpers;
    use RefreshDatabase;

    public function test_it_can_create_an_ai_tutor_conversation(): void
    {
        $conversation = AiTutorConversation::factory()->create();

        $this->assertNotNull($conversation->id);
        $this->assertNotNull($conversation->user_id);
    }

    public function test_uses_uuids(): void
    {
        $conversation = AiTutorConversation::factory()->create();

        $this->assertUsesUuids($conversation);
    }

    public function test_datetime_casts_work(): void
    {
        $conversation = AiTutorConversation::factory()->create([
            'last_message_at' => now(),
        ]);

        $this->assertDatetimeCast($conversation, 'last_message_at');
    }

    public function test_user_relationship_works(): void
    {
        $user = User::factory()->create();
        $conversation = AiTutorConversation::factory()->forUser($user)->create();

        $this->assertBelongsToRelationship($conversation, 'user', User::class);
    }

    public function test_messages_relationship_works(): void
    {
        $conversation = AiTutorConversation::factory()->create();
        AiTutorMessage::factory()->count(3)->forConversation($conversation)->create();

        $this->assertHasManyRelationship($conversation, 'messages', AiTutorMessage::class, 3);
    }

    public function test_for_path_factory_state_works(): void
    {
        $path = LearningPath::factory()->create();
        $conversation = AiTutorConversation::factory()->forPath($path)->create();

        $this->assertEquals($path->id, $conversation->learning_path_id);
    }

    public function test_for_module_factory_state_works(): void
    {
        $module = Module::factory()->create();
        $conversation = AiTutorConversation::factory()->forModule($module)->create();

        $this->assertEquals($module->id, $conversation->module_id);
    }

    public function test_for_step_factory_state_works(): void
    {
        $step = LearningStep::factory()->create();
        $conversation = AiTutorConversation::factory()->forStep($step)->create();

        $this->assertEquals($step->id, $conversation->step_id);
    }

    public function test_archived_factory_state_works(): void
    {
        $conversation = AiTutorConversation::factory()->archived()->create();

        $this->assertEquals('archived', $conversation->status);
    }

    public function test_resolved_factory_state_works(): void
    {
        $conversation = AiTutorConversation::factory()->resolved()->create();

        $this->assertEquals('resolved', $conversation->status);
    }

    public function test_with_messages_factory_state_works(): void
    {
        $conversation = AiTutorConversation::factory()->withMessages(5)->create();

        $this->assertEquals(5, $conversation->total_messages);
        $this->assertGreaterThan(0, $conversation->total_tokens_used);
        $this->assertNotNull($conversation->last_message_at);
    }
}
