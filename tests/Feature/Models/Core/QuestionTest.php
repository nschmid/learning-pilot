<?php

namespace Tests\Feature\Models\Core;

use App\Enums\QuestionType;
use App\Models\AnswerOption;
use App\Models\Assessment;
use App\Models\Question;
use App\Models\QuestionResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ModelTestHelpers;

class QuestionTest extends TestCase
{
    use ModelTestHelpers;
    use RefreshDatabase;

    public function test_it_can_create_a_question(): void
    {
        $question = $this->assertModelCanBeCreated(Question::class);

        $this->assertNotNull($question->question_text);
    }

    public function test_fillable_fields_exist_in_database(): void
    {
        $question = Question::factory()->create();

        $this->assertFillableFieldsExist($question);
    }

    public function test_uses_uuids(): void
    {
        $question = Question::factory()->create();

        $this->assertUsesUuids($question);
    }

    public function test_enum_casts_work(): void
    {
        $question = Question::factory()->singleChoice()->create();

        $this->assertEnumCast($question, 'question_type', QuestionType::class);
    }

    public function test_array_casts_work(): void
    {
        $question = Question::factory()->create([
            'metadata' => ['hint' => 'some hint'],
        ]);

        $this->assertArrayCast($question, 'metadata');
    }

    public function test_assessment_relationship_works(): void
    {
        $assessment = Assessment::factory()->create();
        $question = Question::factory()->forAssessment($assessment)->create();

        $this->assertBelongsToRelationship($question, 'assessment', Assessment::class);
    }

    public function test_options_relationship_works(): void
    {
        $question = Question::factory()->singleChoice()->create();
        AnswerOption::factory()->count(4)->forQuestion($question)->create();

        $this->assertHasManyRelationship($question, 'options', AnswerOption::class);
        $this->assertCount(4, $question->options);
    }

    public function test_responses_relationship_works(): void
    {
        $question = Question::factory()->create();
        QuestionResponse::factory()->count(3)->forQuestion($question)->create();

        $this->assertHasManyRelationship($question, 'responses', QuestionResponse::class);
    }

    public function test_ordered_scope_works(): void
    {
        $assessment = Assessment::factory()->create();
        Question::factory()->forAssessment($assessment)->position(3)->create();
        Question::factory()->forAssessment($assessment)->position(1)->create();

        $questions = Question::ordered()->get();

        $this->assertEquals(1, $questions->first()->position);
    }

    public function test_question_types_can_be_created(): void
    {
        $single = Question::factory()->singleChoice()->create();
        $multiple = Question::factory()->multipleChoice()->create();
        $trueFalse = Question::factory()->trueFalse()->create();
        $text = Question::factory()->text()->create();
        $matching = Question::factory()->matching()->create();

        $this->assertEquals(QuestionType::SingleChoice, $single->question_type);
        $this->assertEquals(QuestionType::MultipleChoice, $multiple->question_type);
        $this->assertEquals(QuestionType::TrueFalse, $trueFalse->question_type);
        $this->assertEquals(QuestionType::Text, $text->question_type);
        $this->assertEquals(QuestionType::Matching, $matching->question_type);
    }

    public function test_is_auto_gradable_helper_works(): void
    {
        $singleChoice = Question::factory()->singleChoice()->create();
        $text = Question::factory()->text()->create();

        $this->assertTrue($singleChoice->isAutoGradable());
        $this->assertFalse($text->isAutoGradable());
    }

    public function test_has_options_helper_works(): void
    {
        $singleChoice = Question::factory()->singleChoice()->create();
        $text = Question::factory()->text()->create();

        $this->assertTrue($singleChoice->hasOptions());
        $this->assertFalse($text->hasOptions());
    }

    public function test_correct_options_helper_works(): void
    {
        $question = Question::factory()->singleChoice()->create();
        AnswerOption::factory()->forQuestion($question)->correct()->create();
        AnswerOption::factory()->count(3)->forQuestion($question)->incorrect()->create();

        $this->assertCount(1, $question->correctOptions());
    }
}
