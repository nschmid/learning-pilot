<?php

namespace Tests\Feature\Models\Core;

use App\Enums\UnlockCondition;
use App\Models\LearningPath;
use App\Models\LearningStep;
use App\Models\Module;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ModelTestHelpers;

class ModuleTest extends TestCase
{
    use ModelTestHelpers;
    use RefreshDatabase;

    public function test_it_can_create_a_module(): void
    {
        $module = $this->assertModelCanBeCreated(Module::class);

        $this->assertNotNull($module->title);
    }

    public function test_fillable_fields_exist_in_database(): void
    {
        $module = Module::factory()->create();

        $this->assertFillableFieldsExist($module);
    }

    public function test_uses_uuids(): void
    {
        $module = Module::factory()->create();

        $this->assertUsesUuids($module);
    }

    public function test_soft_deletes_work(): void
    {
        $module = Module::factory()->create();

        $this->assertSoftDeletes($module);
    }

    public function test_enum_casts_work(): void
    {
        $module = Module::factory()->create(['unlock_condition' => UnlockCondition::Sequential]);

        $this->assertEnumCast($module, 'unlock_condition', UnlockCondition::class);
    }

    public function test_boolean_casts_work(): void
    {
        $module = Module::factory()->create(['is_required' => true]);

        $this->assertBooleanCast($module, 'is_required');
    }

    public function test_learning_path_relationship_works(): void
    {
        $path = LearningPath::factory()->create();
        $module = Module::factory()->forPath($path)->create();

        $this->assertBelongsToRelationship($module, 'learningPath', LearningPath::class);
        $this->assertEquals($path->id, $module->learning_path_id);
    }

    public function test_steps_relationship_works(): void
    {
        $module = Module::factory()->create();
        LearningStep::factory()->count(3)->forModule($module)->create();

        $this->assertHasManyRelationship($module, 'steps', LearningStep::class);
        $this->assertCount(3, $module->steps);
    }

    public function test_ordered_scope_works(): void
    {
        $path = LearningPath::factory()->create();
        Module::factory()->forPath($path)->position(3)->create();
        Module::factory()->forPath($path)->position(1)->create();
        Module::factory()->forPath($path)->position(2)->create();

        $modules = Module::ordered()->get();

        $this->assertEquals(1, $modules->first()->position);
        $this->assertEquals(3, $modules->last()->position);
    }

    public function test_required_scope_filters_correctly(): void
    {
        Module::factory()->count(3)->create(['is_required' => true]);
        Module::factory()->count(2)->optional()->create();

        $this->assertCount(3, Module::required()->get());
    }

    public function test_total_steps_helper_works(): void
    {
        $module = Module::factory()->create();
        LearningStep::factory()->count(5)->forModule($module)->create();

        $this->assertEquals(5, $module->totalSteps());
    }

    public function test_total_points_helper_works(): void
    {
        $module = Module::factory()->create();
        LearningStep::factory()->forModule($module)->create(['points_value' => 10]);
        LearningStep::factory()->forModule($module)->create(['points_value' => 20]);

        $this->assertEquals(30, $module->totalPoints());
    }
}
