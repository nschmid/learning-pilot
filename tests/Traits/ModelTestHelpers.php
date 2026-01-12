<?php

namespace Tests\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

trait ModelTestHelpers
{
    /**
     * Assert that all fillable fields exist as columns in the database.
     */
    protected function assertFillableFieldsExist(Model $model): void
    {
        $table = $model->getTable();
        $fillable = $model->getFillable();
        $columns = Schema::getColumnListing($table);

        foreach ($fillable as $field) {
            $this->assertContains(
                $field,
                $columns,
                "Fillable field '{$field}' does not exist in table '{$table}'. Available columns: ".implode(', ', $columns)
            );
        }
    }

    /**
     * Assert that a model can be created via its factory.
     */
    protected function assertModelCanBeCreated(string $modelClass): Model
    {
        $model = $modelClass::factory()->create();

        $this->assertInstanceOf($modelClass, $model);
        $this->assertNotNull($model->getKey());
        $this->assertDatabaseHas($model->getTable(), [
            $model->getKeyName() => $model->getKey(),
        ]);

        return $model;
    }

    /**
     * Assert that enum casts work correctly.
     */
    protected function assertEnumCast(Model $model, string $attribute, string $enumClass): void
    {
        $value = $model->getAttribute($attribute);

        if ($value !== null) {
            $this->assertInstanceOf(
                $enumClass,
                $value,
                "Attribute '{$attribute}' should be cast to {$enumClass}, got ".gettype($value)
            );
        }
    }

    /**
     * Assert that array/JSON casts work correctly.
     */
    protected function assertArrayCast(Model $model, string $attribute): void
    {
        $value = $model->getAttribute($attribute);

        if ($value !== null) {
            $this->assertIsArray(
                $value,
                "Attribute '{$attribute}' should be cast to array, got ".gettype($value)
            );
        }
    }

    /**
     * Assert that boolean casts work correctly.
     */
    protected function assertBooleanCast(Model $model, string $attribute): void
    {
        $value = $model->getAttribute($attribute);

        if ($value !== null) {
            $this->assertIsBool(
                $value,
                "Attribute '{$attribute}' should be cast to boolean, got ".gettype($value)
            );
        }
    }

    /**
     * Assert that datetime casts work correctly.
     */
    protected function assertDatetimeCast(Model $model, string $attribute): void
    {
        $value = $model->getAttribute($attribute);

        if ($value !== null) {
            $this->assertInstanceOf(
                Carbon::class,
                $value,
                "Attribute '{$attribute}' should be cast to Carbon, got ".gettype($value)
            );
        }
    }

    /**
     * Assert that a BelongsTo relationship works.
     */
    protected function assertBelongsToRelationship(Model $model, string $relationship, string $relatedClass): void
    {
        $this->assertTrue(
            method_exists($model, $relationship),
            "Relationship method '{$relationship}' does not exist on ".get_class($model)
        );

        $relation = $model->{$relationship}();

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            $relation,
            "Relationship '{$relationship}' should be BelongsTo"
        );

        $related = $model->{$relationship};
        if ($related !== null) {
            $this->assertInstanceOf($relatedClass, $related);
        }
    }

    /**
     * Assert that a HasMany relationship works.
     */
    protected function assertHasManyRelationship(Model $model, string $relationship, string $relatedClass): void
    {
        $this->assertTrue(
            method_exists($model, $relationship),
            "Relationship method '{$relationship}' does not exist on ".get_class($model)
        );

        $relation = $model->{$relationship}();

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\HasMany::class,
            $relation,
            "Relationship '{$relationship}' should be HasMany"
        );
    }

    /**
     * Assert that a BelongsToMany relationship works.
     */
    protected function assertBelongsToManyRelationship(Model $model, string $relationship, string $relatedClass): void
    {
        $this->assertTrue(
            method_exists($model, $relationship),
            "Relationship method '{$relationship}' does not exist on ".get_class($model)
        );

        $relation = $model->{$relationship}();

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsToMany::class,
            $relation,
            "Relationship '{$relationship}' should be BelongsToMany"
        );
    }

    /**
     * Assert that a MorphTo relationship works.
     */
    protected function assertMorphToRelationship(Model $model, string $relationship): void
    {
        $this->assertTrue(
            method_exists($model, $relationship),
            "Relationship method '{$relationship}' does not exist on ".get_class($model)
        );

        $relation = $model->{$relationship}();

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\MorphTo::class,
            $relation,
            "Relationship '{$relationship}' should be MorphTo"
        );
    }

    /**
     * Assert that a MorphMany relationship works.
     */
    protected function assertMorphManyRelationship(Model $model, string $relationship): void
    {
        $this->assertTrue(
            method_exists($model, $relationship),
            "Relationship method '{$relationship}' does not exist on ".get_class($model)
        );

        $relation = $model->{$relationship}();

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\MorphMany::class,
            $relation,
            "Relationship '{$relationship}' should be MorphMany"
        );
    }

    /**
     * Assert that a scope filters correctly.
     */
    protected function assertScopeFilters(string $modelClass, string $scope, callable $setup): void
    {
        $setup();

        $scopeMethod = 'scope'.ucfirst($scope);
        $this->assertTrue(
            method_exists($modelClass, $scopeMethod),
            "Scope method '{$scopeMethod}' does not exist on {$modelClass}"
        );

        // Scope should be callable
        $query = $modelClass::query()->{$scope}();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $query);
    }

    /**
     * Assert that soft delete works.
     */
    protected function assertSoftDeletes(Model $model): void
    {
        $this->assertTrue(
            in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($model)),
            get_class($model).' should use SoftDeletes trait'
        );

        $key = $model->getKey();
        $table = $model->getTable();
        $keyName = $model->getKeyName();

        $model->delete();

        $this->assertSoftDeleted($table, [$keyName => $key]);
        $this->assertDatabaseHas($table, [$keyName => $key]);
    }

    /**
     * Assert model uses UUIDs.
     */
    protected function assertUsesUuids(Model $model): void
    {
        $this->assertTrue(
            in_array(\Illuminate\Database\Eloquent\Concerns\HasUuids::class, class_uses_recursive($model)),
            get_class($model).' should use HasUuids trait'
        );

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            (string) $model->getKey(),
            'Model key should be a valid UUID'
        );
    }
}
