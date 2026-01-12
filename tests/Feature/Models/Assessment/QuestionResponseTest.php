<?php

namespace Tests\Feature\Models\Assessment;

use App\Models\AssessmentAttempt;
use App\Models\Question;
use App\Models\QuestionResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ModelTestHelpers;

class QuestionResponseTest extends TestCase
{
    use ModelTestHelpers;
    use RefreshDatabase;

    public function test_it_can_create_a_question_response(): void
    {
        $response = $this->assertModelCanBeCreated(QuestionResponse::class);

        $this->assertNotNull($response->attempt_id);
        $this->assertNotNull($response->question_id);
    }

    public function test_fillable_fields_exist_in_database(): void
    {
        $response = QuestionResponse::factory()->create();

        $this->assertFillableFieldsExist($response);
    }

    public function test_uses_uuids(): void
    {
        $response = QuestionResponse::factory()->create();

        $this->assertUsesUuids($response);
    }

    public function test_boolean_casts_work(): void
    {
        $response = QuestionResponse::factory()->correct()->create();

        $this->assertBooleanCast($response, 'is_correct');
    }

    public function test_attempt_relationship_works(): void
    {
        $attempt = AssessmentAttempt::factory()->create();
        $response = QuestionResponse::factory()->forAttempt($attempt)->create();

        $this->assertBelongsToRelationship($response, 'attempt', AssessmentAttempt::class);
    }

    public function test_question_relationship_works(): void
    {
        $question = Question::factory()->create();
        $response = QuestionResponse::factory()->forQuestion($question)->create();

        $this->assertBelongsToRelationship($response, 'question', Question::class);
    }

    public function test_correct_and_incorrect_factory_states_work(): void
    {
        $correct = QuestionResponse::factory()->correct()->create();
        $incorrect = QuestionResponse::factory()->incorrect()->create();

        $this->assertTrue($correct->is_correct);
        $this->assertFalse($incorrect->is_correct);
        $this->assertEquals(0, $incorrect->points_earned);
    }
}
