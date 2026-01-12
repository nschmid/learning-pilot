<?php

namespace Tests\Feature\Models\AI;

use App\Models\AiTutorConversation;
use App\Models\AiTutorMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ModelTestHelpers;

/**
 * NOTE: AiTutorMessage model has some mismatches with the database migration.
 *
 * Model expects:
 * - tokens_used (combined)
 * - model_used
 * - response_time_ms
 * - metadata
 *
 * Database has:
 * - tokens_input, tokens_output (separate)
 * - model
 * - latency_ms
 * - references
 *
 * Tests below use database schema. Model should be updated to match.
 *
 * @see app/Models/AiTutorMessage.php
 * @see database/migrations/2026_01_10_120303_create_ai_tutor_messages_table.php
 */
class AiTutorMessageTest extends TestCase
{
    use ModelTestHelpers;
    use RefreshDatabase;

    public function test_it_can_create_an_ai_tutor_message(): void
    {
        $message = AiTutorMessage::factory()->create();

        $this->assertNotNull($message->content);
    }

    public function test_uses_uuids(): void
    {
        $message = AiTutorMessage::factory()->create();

        $this->assertUsesUuids($message);
    }

    public function test_datetime_casts_work(): void
    {
        $message = AiTutorMessage::factory()->create([
            'created_at' => now(),
        ]);

        $this->assertDatetimeCast($message, 'created_at');
    }

    public function test_conversation_relationship_works(): void
    {
        $conversation = AiTutorConversation::factory()->create();
        $message = AiTutorMessage::factory()->forConversation($conversation)->create();

        $this->assertBelongsToRelationship($message, 'conversation', AiTutorConversation::class);
    }

    public function test_from_user_factory_state_works(): void
    {
        $message = AiTutorMessage::factory()->fromUser()->create();

        $this->assertEquals('user', $message->role);
        $this->assertNull($message->model);
        $this->assertNull($message->latency_ms);
    }

    public function test_from_assistant_factory_state_works(): void
    {
        $message = AiTutorMessage::factory()->fromAssistant()->create();

        $this->assertEquals('assistant', $message->role);
        $this->assertNotNull($message->model);
    }

    public function test_from_system_factory_state_works(): void
    {
        $message = AiTutorMessage::factory()->fromSystem()->create();

        $this->assertEquals('system', $message->role);
    }

    public function test_role_values_are_valid(): void
    {
        $userMsg = AiTutorMessage::factory()->fromUser()->create();
        $assistantMsg = AiTutorMessage::factory()->fromAssistant()->create();
        $systemMsg = AiTutorMessage::factory()->fromSystem()->create();

        $this->assertEquals('user', $userMsg->role);
        $this->assertEquals('assistant', $assistantMsg->role);
        $this->assertEquals('system', $systemMsg->role);
    }
}
