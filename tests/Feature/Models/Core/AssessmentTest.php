<?php

namespace Tests\Feature\Models\Core;

use App\Enums\AssessmentType;
use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\LearningStep;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ModelTestHelpers;

class AssessmentTest extends TestCase
{
    use ModelTestHelpers;
    use RefreshDatabase;

    public function test_it_can_create_an_assessment(): void
    {
        $assessment = $this->assertModelCanBeCreated(Assessment::class);

        $this->assertNotNull($assessment->title);
    }

    public function test_fillable_fields_exist_in_database(): void
    {
        $assessment = Assessment::factory()->create();

        $this->assertFillableFieldsExist($assessment);
    }

    public function test_uses_uuids(): void
    {
        $assessment = Assessment::factory()->create();

        $this->assertUsesUuids($assessment);
    }

    public function test_enum_casts_work(): void
    {
        $assessment = Assessment::factory()->quiz()->create();

        $this->assertEnumCast($assessment, 'assessment_type', AssessmentType::class);
    }

    public function test_boolean_casts_work(): void
    {
        $assessment = Assessment::factory()->create([
            'shuffle_questions' => true,
            'shuffle_answers' => true,
            'show_correct_answers' => false,
            'show_score_immediately' => true,
        ]);

        $this->assertBooleanCast($assessment, 'shuffle_questions');
        $this->assertBooleanCast($assessment, 'shuffle_answers');
        $this->assertBooleanCast($assessment, 'show_correct_answers');
        $this->assertBooleanCast($assessment, 'show_score_immediately');
    }

    public function test_step_relationship_works(): void
    {
        $step = LearningStep::factory()->assessment()->create();
        $assessment = Assessment::factory()->forStep($step)->create();

        $this->assertBelongsToRelationship($assessment, 'step', LearningStep::class);
    }

    public function test_questions_relationship_works(): void
    {
        $assessment = Assessment::factory()->create();
        Question::factory()->count(5)->forAssessment($assessment)->create();

        $this->assertHasManyRelationship($assessment, 'questions', Question::class);
        $this->assertCount(5, $assessment->questions);
    }

    public function test_attempts_relationship_works(): void
    {
        $assessment = Assessment::factory()->create();
        AssessmentAttempt::factory()->count(3)->forAssessment($assessment)->create();

        $this->assertHasManyRelationship($assessment, 'attempts', AssessmentAttempt::class);
    }

    public function test_assessment_types_can_be_created(): void
    {
        $quiz = Assessment::factory()->quiz()->create();
        $exam = Assessment::factory()->exam()->create();
        $survey = Assessment::factory()->survey()->create();

        $this->assertEquals(AssessmentType::Quiz, $quiz->assessment_type);
        $this->assertEquals(AssessmentType::Exam, $exam->assessment_type);
        $this->assertEquals(AssessmentType::Survey, $survey->assessment_type);
    }

    public function test_total_points_helper_works(): void
    {
        $assessment = Assessment::factory()->create();
        Question::factory()->forAssessment($assessment)->create(['points' => 10]);
        Question::factory()->forAssessment($assessment)->create(['points' => 20]);

        $this->assertEquals(30, $assessment->totalPoints());
    }

    public function test_question_count_helper_works(): void
    {
        $assessment = Assessment::factory()->create();
        Question::factory()->count(7)->forAssessment($assessment)->create();

        $this->assertEquals(7, $assessment->questionCount());
    }

    public function test_has_time_limit_helper_works(): void
    {
        $withLimit = Assessment::factory()->withTimeLimit(60)->create();
        $withoutLimit = Assessment::factory()->create(['time_limit_minutes' => null]);

        $this->assertTrue($withLimit->hasTimeLimit());
        $this->assertFalse($withoutLimit->hasTimeLimit());
    }

    public function test_has_attempt_limit_helper_works(): void
    {
        $withLimit = Assessment::factory()->create(['max_attempts' => 3]);
        $withoutLimit = Assessment::factory()->unlimitedAttempts()->create();

        $this->assertTrue($withLimit->hasAttemptLimit());
        $this->assertFalse($withoutLimit->hasAttemptLimit());
    }
}
