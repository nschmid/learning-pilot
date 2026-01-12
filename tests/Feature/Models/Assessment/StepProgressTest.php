<?php

namespace Tests\Feature\Models\Assessment;

use App\Enums\StepProgressStatus;
use App\Models\AiGeneratedContent;
use App\Models\Enrollment;
use App\Models\LearningStep;
use App\Models\StepProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ModelTestHelpers;

class StepProgressTest extends TestCase
{
    use ModelTestHelpers;
    use RefreshDatabase;

    public function test_it_can_create_a_step_progress(): void
    {
        $progress = $this->assertModelCanBeCreated(StepProgress::class);

        $this->assertNotNull($progress->enrollment_id);
        $this->assertNotNull($progress->step_id);
    }

    public function test_fillable_fields_exist_in_database(): void
    {
        $progress = StepProgress::factory()->create();

        $this->assertFillableFieldsExist($progress);
    }

    public function test_uses_uuids(): void
    {
        $progress = StepProgress::factory()->create();

        $this->assertUsesUuids($progress);
    }

    public function test_enum_casts_work(): void
    {
        $progress = StepProgress::factory()->inProgress()->create();

        $this->assertEnumCast($progress, 'status', StepProgressStatus::class);
    }

    public function test_datetime_casts_work(): void
    {
        $progress = StepProgress::factory()->completed()->create();

        $this->assertDatetimeCast($progress, 'started_at');
        $this->assertDatetimeCast($progress, 'completed_at');
    }

    public function test_array_casts_work(): void
    {
        $progress = StepProgress::factory()->create([
            'data' => ['attempts' => 3],
        ]);

        $this->assertArrayCast($progress, 'data');
    }

    public function test_enrollment_relationship_works(): void
    {
        $enrollment = Enrollment::factory()->create();
        $progress = StepProgress::factory()->forEnrollment($enrollment)->create();

        $this->assertBelongsToRelationship($progress, 'enrollment', Enrollment::class);
    }

    public function test_step_relationship_works(): void
    {
        $step = LearningStep::factory()->create();
        $progress = StepProgress::factory()->forStep($step)->create();

        $this->assertBelongsToRelationship($progress, 'step', LearningStep::class);
    }

    public function test_ai_generated_contents_relationship_works(): void
    {
        $progress = StepProgress::factory()->create();
        AiGeneratedContent::factory()->count(2)->forContentable($progress)->create();

        $this->assertMorphManyRelationship($progress, 'aiGeneratedContents');
    }

    public function test_completed_scope_works(): void
    {
        StepProgress::factory()->count(3)->completed()->create();
        StepProgress::factory()->count(2)->inProgress()->create();

        $this->assertCount(3, StepProgress::completed()->get());
    }

    public function test_in_progress_scope_works(): void
    {
        StepProgress::factory()->count(2)->completed()->create();
        StepProgress::factory()->count(3)->inProgress()->create();

        $this->assertCount(3, StepProgress::inProgress()->get());
    }

    public function test_is_completed_helper_works(): void
    {
        $completed = StepProgress::factory()->completed()->create();
        $inProgress = StepProgress::factory()->inProgress()->create();

        $this->assertTrue($completed->isCompleted());
        $this->assertFalse($inProgress->isCompleted());
    }

    public function test_is_in_progress_helper_works(): void
    {
        $completed = StepProgress::factory()->completed()->create();
        $inProgress = StepProgress::factory()->inProgress()->create();

        $this->assertFalse($completed->isInProgress());
        $this->assertTrue($inProgress->isInProgress());
    }

    public function test_get_formatted_time_spent_works(): void
    {
        $progress = StepProgress::factory()->create([
            'time_spent_seconds' => 125,
        ]);

        $this->assertEquals('2:05', $progress->getFormattedTimeSpent());
    }
}
