<?php

namespace Tests\Feature\Models\Assessment;

use App\Enums\SubmissionStatus;
use App\Models\Enrollment;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ModelTestHelpers;

class TaskSubmissionTest extends TestCase
{
    use ModelTestHelpers;
    use RefreshDatabase;

    public function test_it_can_create_a_task_submission(): void
    {
        $submission = $this->assertModelCanBeCreated(TaskSubmission::class);

        $this->assertNotNull($submission->task_id);
        $this->assertNotNull($submission->enrollment_id);
    }

    public function test_fillable_fields_exist_in_database(): void
    {
        $submission = TaskSubmission::factory()->create();

        $this->assertFillableFieldsExist($submission);
    }

    public function test_uses_uuids(): void
    {
        $submission = TaskSubmission::factory()->create();

        $this->assertUsesUuids($submission);
    }

    public function test_enum_casts_work(): void
    {
        $submission = TaskSubmission::factory()->pending()->create();

        $this->assertEnumCast($submission, 'status', SubmissionStatus::class);
    }

    public function test_datetime_casts_work(): void
    {
        $submission = TaskSubmission::factory()->reviewed()->create();

        $this->assertDatetimeCast($submission, 'submitted_at');
        $this->assertDatetimeCast($submission, 'reviewed_at');
    }

    public function test_array_casts_work(): void
    {
        $submission = TaskSubmission::factory()->withFiles()->create();

        $this->assertArrayCast($submission, 'file_paths');
    }

    public function test_task_relationship_works(): void
    {
        $task = Task::factory()->create();
        $submission = TaskSubmission::factory()->forTask($task)->create();

        $this->assertBelongsToRelationship($submission, 'task', Task::class);
    }

    public function test_enrollment_relationship_works(): void
    {
        $enrollment = Enrollment::factory()->create();
        $submission = TaskSubmission::factory()->forEnrollment($enrollment)->create();

        $this->assertBelongsToRelationship($submission, 'enrollment', Enrollment::class);
    }

    public function test_reviewer_relationship_works(): void
    {
        $submission = TaskSubmission::factory()->reviewed()->create();

        $this->assertBelongsToRelationship($submission, 'reviewer', User::class);
    }

    public function test_pending_scope_works(): void
    {
        TaskSubmission::factory()->count(3)->pending()->create();
        TaskSubmission::factory()->count(2)->reviewed()->create();

        $this->assertCount(3, TaskSubmission::pending()->get());
    }

    public function test_reviewed_scope_works(): void
    {
        TaskSubmission::factory()->count(2)->pending()->create();
        TaskSubmission::factory()->count(3)->reviewed()->create();

        $this->assertCount(3, TaskSubmission::reviewed()->get());
    }

    public function test_status_helper_methods_work(): void
    {
        $pending = TaskSubmission::factory()->pending()->create();
        $reviewed = TaskSubmission::factory()->reviewed()->create();
        $revisionRequested = TaskSubmission::factory()->revisionRequested()->create();

        $this->assertTrue($pending->isPending());
        $this->assertFalse($pending->isReviewed());

        $this->assertFalse($reviewed->isPending());
        $this->assertTrue($reviewed->isReviewed());

        $this->assertTrue($revisionRequested->needsRevision());
    }

    public function test_score_percent_helper_works(): void
    {
        $task = Task::factory()->create(['max_points' => 100]);
        $submission = TaskSubmission::factory()->forTask($task)->create([
            'status' => SubmissionStatus::Reviewed,
            'score' => 85,
        ]);

        $this->assertEquals(85.0, $submission->scorePercent());
    }
}
