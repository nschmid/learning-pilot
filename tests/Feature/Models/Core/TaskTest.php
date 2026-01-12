<?php

namespace Tests\Feature\Models\Core;

use App\Enums\TaskType;
use App\Models\LearningStep;
use App\Models\Task;
use App\Models\TaskSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ModelTestHelpers;

class TaskTest extends TestCase
{
    use ModelTestHelpers;
    use RefreshDatabase;

    public function test_it_can_create_a_task(): void
    {
        $task = $this->assertModelCanBeCreated(Task::class);

        $this->assertNotNull($task->title);
    }

    public function test_fillable_fields_exist_in_database(): void
    {
        $task = Task::factory()->create();

        $this->assertFillableFieldsExist($task);
    }

    public function test_uses_uuids(): void
    {
        $task = Task::factory()->create();

        $this->assertUsesUuids($task);
    }

    public function test_enum_casts_work(): void
    {
        $task = Task::factory()->submission()->create();

        $this->assertEnumCast($task, 'task_type', TaskType::class);
    }

    public function test_boolean_casts_work(): void
    {
        $task = Task::factory()->create([
            'allow_late' => true,
            'allow_resubmit' => false,
        ]);

        $this->assertBooleanCast($task, 'allow_late');
        $this->assertBooleanCast($task, 'allow_resubmit');
    }

    public function test_array_casts_work(): void
    {
        $task = Task::factory()->create([
            'rubric' => [['criterion' => 'Quality', 'max_points' => 50]],
            'allowed_file_types' => ['pdf', 'docx'],
        ]);

        $this->assertArrayCast($task, 'rubric');
        $this->assertArrayCast($task, 'allowed_file_types');
    }

    public function test_step_relationship_works(): void
    {
        $step = LearningStep::factory()->task()->create();
        $task = Task::factory()->forStep($step)->create();

        $this->assertBelongsToRelationship($task, 'step', LearningStep::class);
    }

    public function test_submissions_relationship_works(): void
    {
        $task = Task::factory()->create();
        TaskSubmission::factory()->count(3)->forTask($task)->create();

        $this->assertHasManyRelationship($task, 'submissions', TaskSubmission::class);
        $this->assertCount(3, $task->submissions);
    }

    public function test_task_types_can_be_created(): void
    {
        $submission = Task::factory()->submission()->create();
        $project = Task::factory()->project()->create();
        $discussion = Task::factory()->discussion()->create();

        $this->assertEquals(TaskType::Submission, $submission->task_type);
        $this->assertEquals(TaskType::Project, $project->task_type);
        $this->assertEquals(TaskType::Discussion, $discussion->task_type);
    }
}
