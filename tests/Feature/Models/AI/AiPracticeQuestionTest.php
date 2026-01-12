<?php

namespace Tests\Feature\Models\AI;

use App\Enums\QuestionType;
use App\Models\AiPracticeQuestion;
use App\Models\AiPracticeSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ModelTestHelpers;

class AiPracticeQuestionTest extends TestCase
{
    use ModelTestHelpers;
    use RefreshDatabase;

    public function test_it_can_create_an_ai_practice_question(): void
    {
        $question = $this->assertModelCanBeCreated(AiPracticeQuestion::class);

        $this->assertNotNull($question->question_text);
    }

    public function test_fillable_fields_exist_in_database(): void
    {
        $question = AiPracticeQuestion::factory()->create();

        $this->assertFillableFieldsExist($question);
    }

    public function test_uses_uuids(): void
    {
        $question = AiPracticeQuestion::factory()->create();

        $this->assertUsesUuids($question);
    }

    public function test_datetime_casts_work(): void
    {
        $question = AiPracticeQuestion::factory()->answeredCorrectly()->create();

        $this->assertDatetimeCast($question, 'answered_at');
    }

    public function test_boolean_casts_work(): void
    {
        $question = AiPracticeQuestion::factory()->answeredCorrectly()->create();

        $this->assertBooleanCast($question, 'is_correct');
    }

    public function test_enum_casts_work(): void
    {
        $question = AiPracticeQuestion::factory()->create([
            'question_type' => QuestionType::SingleChoice,
        ]);

        $this->assertEnumCast($question, 'question_type', QuestionType::class);
    }

    public function test_array_casts_work(): void
    {
        $question = AiPracticeQuestion::factory()->create([
            'options' => ['A' => 'Option A', 'B' => 'Option B'],
            'topics' => ['math', 'algebra'],
        ]);

        $this->assertArrayCast($question, 'options');
        $this->assertArrayCast($question, 'topics');
    }

    public function test_session_relationship_works(): void
    {
        $session = AiPracticeSession::factory()->create();
        $question = AiPracticeQuestion::factory()->forSession($session)->create();

        $this->assertBelongsToRelationship($question, 'session', AiPracticeSession::class);
    }

    public function test_answered_scope_works(): void
    {
        AiPracticeQuestion::factory()->count(3)->answeredCorrectly()->create();
        AiPracticeQuestion::factory()->count(2)->create(['answered_at' => null]);

        $this->assertCount(3, AiPracticeQuestion::answered()->get());
    }

    public function test_unanswered_scope_works(): void
    {
        AiPracticeQuestion::factory()->count(2)->answeredCorrectly()->create();
        AiPracticeQuestion::factory()->count(4)->create(['answered_at' => null]);

        $this->assertCount(4, AiPracticeQuestion::unanswered()->get());
    }

    public function test_correct_scope_works(): void
    {
        AiPracticeQuestion::factory()->count(3)->answeredCorrectly()->create();
        AiPracticeQuestion::factory()->count(2)->answeredIncorrectly()->create();

        $this->assertCount(3, AiPracticeQuestion::correct()->get());
    }

    public function test_incorrect_scope_works(): void
    {
        AiPracticeQuestion::factory()->count(2)->answeredCorrectly()->create();
        AiPracticeQuestion::factory()->count(4)->answeredIncorrectly()->create();

        $this->assertCount(4, AiPracticeQuestion::incorrect()->get());
    }

    public function test_by_difficulty_scope_works(): void
    {
        AiPracticeQuestion::factory()->count(3)->difficulty('beginner')->create();
        AiPracticeQuestion::factory()->count(2)->difficulty('advanced')->create();

        $this->assertCount(3, AiPracticeQuestion::byDifficulty('beginner')->get());
        $this->assertCount(2, AiPracticeQuestion::byDifficulty('advanced')->get());
    }

    public function test_is_answered_helper_works(): void
    {
        $answered = AiPracticeQuestion::factory()->answeredCorrectly()->create();
        $unanswered = AiPracticeQuestion::factory()->create(['answered_at' => null]);

        $this->assertTrue($answered->isAnswered());
        $this->assertFalse($unanswered->isAnswered());
    }

    public function test_answer_helper_works_for_correct_answer(): void
    {
        $session = AiPracticeSession::factory()->create([
            'questions_answered' => 0,
            'correct_answers' => 0,
        ]);

        $question = AiPracticeQuestion::factory()->forSession($session)->create([
            'correct_answer' => 'A',
            'answered_at' => null,
        ]);

        $result = $question->answer('A');

        $this->assertTrue($result);
        $this->assertTrue($question->fresh()->is_correct);
        $this->assertEquals('A', $question->fresh()->user_answer);
        $this->assertNotNull($question->fresh()->answered_at);
    }

    public function test_answer_helper_works_for_incorrect_answer(): void
    {
        $session = AiPracticeSession::factory()->create([
            'questions_answered' => 0,
            'correct_answers' => 0,
        ]);

        $question = AiPracticeQuestion::factory()->forSession($session)->create([
            'correct_answer' => 'A',
            'answered_at' => null,
        ]);

        $result = $question->answer('B');

        $this->assertFalse($result);
        $this->assertFalse($question->fresh()->is_correct);
        $this->assertEquals('B', $question->fresh()->user_answer);
    }

    public function test_answer_helper_records_time_spent(): void
    {
        $session = AiPracticeSession::factory()->create([
            'questions_answered' => 0,
            'correct_answers' => 0,
        ]);

        $question = AiPracticeQuestion::factory()->forSession($session)->create([
            'correct_answer' => 'A',
            'answered_at' => null,
        ]);

        $question->answer('A', 45);

        $this->assertEquals(45, $question->fresh()->time_spent_seconds);
    }

    public function test_answer_helper_updates_session_counters(): void
    {
        $session = AiPracticeSession::factory()->create([
            'question_count' => 10,
            'questions_answered' => 0,
            'correct_answers' => 0,
        ]);

        $question = AiPracticeQuestion::factory()->forSession($session)->create([
            'correct_answer' => 'A',
        ]);

        $question->answer('A');

        $this->assertEquals(1, $session->fresh()->questions_answered);
        $this->assertEquals(1, $session->fresh()->correct_answers);
    }

    public function test_check_answer_handles_true_false_questions(): void
    {
        $session = AiPracticeSession::factory()->create();
        $question = AiPracticeQuestion::factory()->forSession($session)->trueFalse()->create([
            'correct_answer' => 'True',
        ]);

        // Case insensitive comparison
        $result = $question->answer('true');

        $this->assertTrue($result);
    }

    public function test_check_answer_handles_multiple_choice_questions(): void
    {
        $session = AiPracticeSession::factory()->create();
        $question = AiPracticeQuestion::factory()->forSession($session)->multipleChoice()->create([
            'correct_answer' => 'A,B',
        ]);

        $result = $question->answer('B,A');

        $this->assertTrue($result);
    }

    public function test_answered_correctly_factory_state_works(): void
    {
        $question = AiPracticeQuestion::factory()->answeredCorrectly()->create();

        $this->assertTrue($question->is_correct);
        $this->assertNotNull($question->user_answer);
        $this->assertNotNull($question->answered_at);
        $this->assertEquals($question->correct_answer, $question->user_answer);
    }

    public function test_answered_incorrectly_factory_state_works(): void
    {
        $question = AiPracticeQuestion::factory()->answeredIncorrectly()->create();

        $this->assertFalse($question->is_correct);
        $this->assertNotNull($question->user_answer);
        $this->assertNotNull($question->answered_at);
        $this->assertNotEquals($question->correct_answer, $question->user_answer);
    }

    public function test_true_false_factory_state_works(): void
    {
        $question = AiPracticeQuestion::factory()->trueFalse()->create();

        $this->assertEquals(QuestionType::TrueFalse, $question->question_type);
        $this->assertContains($question->correct_answer, ['True', 'False']);
    }

    public function test_multiple_choice_factory_state_works(): void
    {
        $question = AiPracticeQuestion::factory()->multipleChoice()->create();

        $this->assertEquals(QuestionType::MultipleChoice, $question->question_type);
    }

    public function test_difficulty_factory_state_works(): void
    {
        $question = AiPracticeQuestion::factory()->difficulty('expert')->create();

        $this->assertEquals('expert', $question->difficulty);
    }

    public function test_with_topics_factory_state_works(): void
    {
        $question = AiPracticeQuestion::factory()->withTopics(['math', 'algebra'])->create();

        $this->assertEquals(['math', 'algebra'], $question->topics);
    }

    public function test_at_position_factory_state_works(): void
    {
        $question = AiPracticeQuestion::factory()->atPosition(5)->create();

        $this->assertEquals(5, $question->position);
    }

    public function test_with_feedback_factory_state_works(): void
    {
        $question = AiPracticeQuestion::factory()->withFeedback('Great job!')->create();

        $this->assertEquals('Great job!', $question->ai_feedback);
    }
}
