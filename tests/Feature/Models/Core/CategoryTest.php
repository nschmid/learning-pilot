<?php

namespace Tests\Feature\Models\Core;

use App\Models\Category;
use App\Models\LearningPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ModelTestHelpers;

class CategoryTest extends TestCase
{
    use ModelTestHelpers;
    use RefreshDatabase;

    public function test_it_can_create_a_category(): void
    {
        $category = $this->assertModelCanBeCreated(Category::class);

        $this->assertNotNull($category->name);
        $this->assertNotNull($category->slug);
    }

    public function test_fillable_fields_exist_in_database(): void
    {
        $category = Category::factory()->create();

        $this->assertFillableFieldsExist($category);
    }

    public function test_boolean_casts_work(): void
    {
        $category = Category::factory()->create(['is_active' => true]);

        $this->assertBooleanCast($category, 'is_active');
    }

    public function test_slug_is_auto_generated(): void
    {
        $category = Category::factory()->create(['name' => 'Test Category Name']);

        $this->assertEquals('test-category-name', $category->slug);
    }

    public function test_parent_relationship_works(): void
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->withParent($parent)->create();

        $this->assertBelongsToRelationship($child, 'parent', Category::class);
        $this->assertEquals($parent->id, $child->parent_id);
    }

    public function test_children_relationship_works(): void
    {
        $parent = Category::factory()->create();
        Category::factory()->count(3)->withParent($parent)->create();

        $this->assertHasManyRelationship($parent, 'children', Category::class);
        $this->assertCount(3, $parent->children);
    }

    public function test_learning_paths_relationship_works(): void
    {
        $category = Category::factory()->create();
        LearningPath::factory()->count(2)->create(['category_id' => $category->id]);

        $this->assertHasManyRelationship($category, 'learningPaths', LearningPath::class);
        $this->assertCount(2, $category->learningPaths);
    }

    public function test_active_scope_filters_correctly(): void
    {
        Category::factory()->count(3)->create(['is_active' => true]);
        Category::factory()->count(2)->create(['is_active' => false]);

        $this->assertCount(3, Category::active()->get());
    }

    public function test_root_scope_filters_correctly(): void
    {
        Category::factory()->count(3)->create(['parent_id' => null]);
        $parent = Category::factory()->create();
        Category::factory()->count(2)->withParent($parent)->create();

        $this->assertCount(4, Category::root()->get()); // 3 + 1 parent
    }

    public function test_ordered_scope_works(): void
    {
        Category::factory()->create(['position' => 3]);
        Category::factory()->create(['position' => 1]);
        Category::factory()->create(['position' => 2]);

        $categories = Category::ordered()->get();

        $this->assertEquals(1, $categories->first()->position);
        $this->assertEquals(3, $categories->last()->position);
    }
}
