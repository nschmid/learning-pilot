<?php

namespace Tests\Feature\Models\Core;

use App\Enums\StepType;
use App\Models\Assessment;
use App\Models\LearningMaterial;
use App\Models\LearningStep;
use App\Models\Module;
use App\Models\StepProgress;
use App\Models\Task;
use App\Models\UserNote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ModelTestHelpers;

class LearningStepTest extends TestCase
{
    use ModelTestHelpers;
    use RefreshDatabase;

    public function test_it_can_create_a_learning_step(): void
    {
        $step = $this->assertModelCanBeCreated(LearningStep::class);

        $this->assertNotNull($step->title);
    }

    public function test_fillable_fields_exist_in_database(): void
    {
        $step = LearningStep::factory()->create();

        $this->assertFillableFieldsExist($step);
    }

    public function test_uses_uuids(): void
    {
        $step = LearningStep::factory()->create();

        $this->assertUsesUuids($step);
    }

    public function test_soft_deletes_work(): void
    {
        $step = LearningStep::factory()->create();

        $this->assertSoftDeletes($step);
    }

    public function test_enum_casts_work(): void
    {
        $step = LearningStep::factory()->material()->create();

        $this->assertEnumCast($step, 'step_type', StepType::class);
        $this->assertEquals(StepType::Material, $step->step_type);
    }

    public function test_boolean_casts_work(): void
    {
        $step = LearningStep::factory()->create([
            'is_required' => true,
            'is_preview' => false,
        ]);

        $this->assertBooleanCast($step, 'is_required');
        $this->assertBooleanCast($step, 'is_preview');
    }

    public function test_module_relationship_works(): void
    {
        $module = Module::factory()->create();
        $step = LearningStep::factory()->forModule($module)->create();

        $this->assertBelongsToRelationship($step, 'module', Module::class);
        $this->assertEquals($module->id, $step->module_id);
    }

    public function test_materials_relationship_works(): void
    {
        $step = LearningStep::factory()->create();
        LearningMaterial::factory()->count(3)->forStep($step)->create();

        $this->assertHasManyRelationship($step, 'materials', LearningMaterial::class);
        $this->assertCount(3, $step->materials);
    }

    public function test_task_relationship_works(): void
    {
        $step = LearningStep::factory()->task()->create();
        Task::factory()->forStep($step)->create();

        $related = $step->task;
        $this->assertInstanceOf(Task::class, $related);
    }

    public function test_assessment_relationship_works(): void
    {
        $step = LearningStep::factory()->assessment()->create();
        Assessment::factory()->forStep($step)->create();

        $related = $step->assessment;
        $this->assertInstanceOf(Assessment::class, $related);
    }

    public function test_progress_relationship_works(): void
    {
        $step = LearningStep::factory()->create();
        StepProgress::factory()->count(2)->forStep($step)->create();

        $this->assertHasManyRelationship($step, 'progress', StepProgress::class);
    }

    public function test_notes_relationship_works(): void
    {
        $step = LearningStep::factory()->create();
        UserNote::factory()->count(2)->forStep($step)->create();

        $this->assertHasManyRelationship($step, 'notes', UserNote::class);
    }

    public function test_ordered_scope_works(): void
    {
        $module = Module::factory()->create();
        LearningStep::factory()->forModule($module)->position(3)->create();
        LearningStep::factory()->forModule($module)->position(1)->create();
        LearningStep::factory()->forModule($module)->position(2)->create();

        $steps = LearningStep::ordered()->get();

        $this->assertEquals(1, $steps->first()->position);
    }

    public function test_required_scope_works(): void
    {
        LearningStep::factory()->count(3)->create(['is_required' => true]);
        LearningStep::factory()->count(2)->optional()->create();

        $this->assertCount(3, LearningStep::required()->get());
    }

    public function test_by_type_scope_works(): void
    {
        LearningStep::factory()->count(2)->material()->create();
        LearningStep::factory()->count(3)->task()->create();
        LearningStep::factory()->count(1)->assessment()->create();

        $this->assertCount(2, LearningStep::byType(StepType::Material)->get());
        $this->assertCount(3, LearningStep::byType(StepType::Task)->get());
        $this->assertCount(1, LearningStep::byType(StepType::Assessment)->get());
    }

    public function test_step_type_helper_methods_work(): void
    {
        $materialStep = LearningStep::factory()->material()->create();
        $taskStep = LearningStep::factory()->task()->create();
        $assessmentStep = LearningStep::factory()->assessment()->create();

        $this->assertTrue($materialStep->isMaterial());
        $this->assertFalse($materialStep->isTask());
        $this->assertFalse($materialStep->isAssessment());

        $this->assertTrue($taskStep->isTask());
        $this->assertTrue($assessmentStep->isAssessment());
    }
}
