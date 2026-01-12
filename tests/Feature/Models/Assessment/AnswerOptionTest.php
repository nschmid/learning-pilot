<?php

namespace Tests\Feature\Models\Assessment;

use App\Models\AnswerOption;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ModelTestHelpers;

class AnswerOptionTest extends TestCase
{
    use ModelTestHelpers;
    use RefreshDatabase;

    public function test_it_can_create_an_answer_option(): void
    {
        $option = $this->assertModelCanBeCreated(AnswerOption::class);

        $this->assertNotNull($option->option_text);
    }

    public function test_fillable_fields_exist_in_database(): void
    {
        $option = AnswerOption::factory()->create();

        $this->assertFillableFieldsExist($option);
    }

    public function test_uses_uuids(): void
    {
        $option = AnswerOption::factory()->create();

        $this->assertUsesUuids($option);
    }

    public function test_boolean_casts_work(): void
    {
        $option = AnswerOption::factory()->create(['is_correct' => true]);

        $this->assertBooleanCast($option, 'is_correct');
    }

    public function test_question_relationship_works(): void
    {
        $question = Question::factory()->create();
        $option = AnswerOption::factory()->forQuestion($question)->create();

        $this->assertBelongsToRelationship($option, 'question', Question::class);
    }

    public function test_ordered_scope_works(): void
    {
        $question = Question::factory()->create();
        AnswerOption::factory()->forQuestion($question)->position(3)->create();
        AnswerOption::factory()->forQuestion($question)->position(1)->create();

        $options = AnswerOption::ordered()->get();

        $this->assertEquals(1, $options->first()->position);
    }

    public function test_correct_scope_works(): void
    {
        $question = Question::factory()->create();
        AnswerOption::factory()->count(2)->forQuestion($question)->correct()->create();
        AnswerOption::factory()->count(3)->forQuestion($question)->incorrect()->create();

        $this->assertCount(2, AnswerOption::correct()->get());
    }
}
