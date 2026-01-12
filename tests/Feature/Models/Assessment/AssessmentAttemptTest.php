<?php

namespace Tests\Feature\Models\Assessment;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Enrollment;
use App\Models\QuestionResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ModelTestHelpers;

class AssessmentAttemptTest extends TestCase
{
    use ModelTestHelpers;
    use RefreshDatabase;

    public function test_it_can_create_an_assessment_attempt(): void
    {
        $attempt = $this->assertModelCanBeCreated(AssessmentAttempt::class);

        $this->assertNotNull($attempt->assessment_id);
        $this->assertNotNull($attempt->enrollment_id);
    }

    public function test_fillable_fields_exist_in_database(): void
    {
        $attempt = AssessmentAttempt::factory()->create();

        $this->assertFillableFieldsExist($attempt);
    }

    public function test_uses_uuids(): void
    {
        $attempt = AssessmentAttempt::factory()->create();

        $this->assertUsesUuids($attempt);
    }

    public function test_datetime_casts_work(): void
    {
        $attempt = AssessmentAttempt::factory()->completed()->create();

        $this->assertDatetimeCast($attempt, 'started_at');
        $this->assertDatetimeCast($attempt, 'completed_at');
    }

    public function test_boolean_casts_work(): void
    {
        $attempt = AssessmentAttempt::factory()->passed()->create();

        $this->assertBooleanCast($attempt, 'passed');
    }

    public function test_array_casts_work(): void
    {
        $attempt = AssessmentAttempt::factory()->create([
            'answers' => ['q1' => 'A', 'q2' => 'B'],
        ]);

        $this->assertArrayCast($attempt, 'answers');
    }

    public function test_assessment_relationship_works(): void
    {
        $assessment = Assessment::factory()->create();
        $attempt = AssessmentAttempt::factory()->forAssessment($assessment)->create();

        $this->assertBelongsToRelationship($attempt, 'assessment', Assessment::class);
    }

    public function test_enrollment_relationship_works(): void
    {
        $enrollment = Enrollment::factory()->create();
        $attempt = AssessmentAttempt::factory()->forEnrollment($enrollment)->create();

        $this->assertBelongsToRelationship($attempt, 'enrollment', Enrollment::class);
    }

    public function test_responses_relationship_works(): void
    {
        $attempt = AssessmentAttempt::factory()->create();
        QuestionResponse::factory()->count(5)->forAttempt($attempt)->create();

        $this->assertHasManyRelationship($attempt, 'responses', QuestionResponse::class);
        $this->assertCount(5, $attempt->responses);
    }

    public function test_passed_scope_works(): void
    {
        AssessmentAttempt::factory()->count(3)->passed()->create();
        AssessmentAttempt::factory()->count(2)->failed()->create();

        $this->assertCount(3, AssessmentAttempt::passed()->get());
    }

    public function test_completed_scope_works(): void
    {
        AssessmentAttempt::factory()->count(3)->completed()->create();
        AssessmentAttempt::factory()->count(2)->inProgress()->create();

        $this->assertCount(3, AssessmentAttempt::completed()->get());
    }

    public function test_is_completed_helper_works(): void
    {
        $completed = AssessmentAttempt::factory()->completed()->create();
        $inProgress = AssessmentAttempt::factory()->inProgress()->create();

        $this->assertTrue($completed->isCompleted());
        $this->assertFalse($inProgress->isCompleted());
    }

    public function test_is_in_progress_helper_works(): void
    {
        $completed = AssessmentAttempt::factory()->completed()->create();
        $inProgress = AssessmentAttempt::factory()->inProgress()->create();

        $this->assertFalse($completed->isInProgress());
        $this->assertTrue($inProgress->isInProgress());
    }

    public function test_has_passed_helper_works(): void
    {
        $passed = AssessmentAttempt::factory()->passed()->create();
        $failed = AssessmentAttempt::factory()->failed()->create();

        $this->assertTrue($passed->hasPassed());
        $this->assertFalse($failed->hasPassed());
    }

    public function test_get_formatted_time_spent_works(): void
    {
        $attempt = AssessmentAttempt::factory()->create([
            'time_spent_seconds' => 125,
        ]);

        $this->assertEquals('2:05', $attempt->getFormattedTimeSpent());
    }
}
