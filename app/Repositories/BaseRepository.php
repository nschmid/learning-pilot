<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements RepositoryInterface
{
    protected Model $model;

    protected Builder $query;

    public function __construct()
    {
        $this->model = $this->makeModel();
        $this->resetQuery();
    }

    /**
     * Specify the model class name.
     */
    abstract protected function model(): string;

    /**
     * Create a new model instance.
     */
    protected function makeModel(): Model
    {
        $model = app($this->model());

        if (! $model instanceof Model) {
            throw new \RuntimeException("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $model;
    }

    /**
     * Reset the query builder.
     */
    protected function resetQuery(): self
    {
        $this->query = $this->model->newQuery();

        return $this;
    }

    /**
     * Get all records.
     */
    public function all(array $columns = ['*']): Collection
    {
        $result = $this->query->get($columns);
        $this->resetQuery();

        return $result;
    }

    /**
     * Get paginated records.
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        $result = $this->query->paginate($perPage, $columns);
        $this->resetQuery();

        return $result;
    }

    /**
     * Find a record by ID.
     */
    public function find(string $id, array $columns = ['*']): ?Model
    {
        $result = $this->query->find($id, $columns);
        $this->resetQuery();

        return $result;
    }

    /**
     * Find a record by ID or fail.
     */
    public function findOrFail(string $id, array $columns = ['*']): Model
    {
        $result = $this->query->findOrFail($id, $columns);
        $this->resetQuery();

        return $result;
    }

    /**
     * Find records by a field.
     */
    public function findBy(string $field, mixed $value, array $columns = ['*']): Collection
    {
        $result = $this->query->where($field, $value)->get($columns);
        $this->resetQuery();

        return $result;
    }

    /**
     * Find first record by a field.
     */
    public function findFirstBy(string $field, mixed $value, array $columns = ['*']): ?Model
    {
        $result = $this->query->where($field, $value)->first($columns);
        $this->resetQuery();

        return $result;
    }

    /**
     * Find a record by slug.
     */
    public function findBySlug(string $slug, array $columns = ['*']): ?Model
    {
        return $this->findFirstBy('slug', $slug, $columns);
    }

    /**
     * Create a new record.
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update a record.
     */
    public function update(string $id, array $data): bool
    {
        $record = $this->find($id);

        if (! $record) {
            return false;
        }

        return $record->update($data);
    }

    /**
     * Delete a record.
     */
    public function delete(string $id): bool
    {
        $record = $this->find($id);

        if (! $record) {
            return false;
        }

        return $record->delete();
    }

    /**
     * Get records with relationships.
     */
    public function with(array $relations): self
    {
        $this->query = $this->query->with($relations);

        return $this;
    }

    /**
     * Order records by a column.
     */
    public function orderBy(string $column, string $direction = 'asc'): self
    {
        $this->query = $this->query->orderBy($column, $direction);

        return $this;
    }

    /**
     * Add a where clause.
     */
    public function where(string $column, mixed $operator, mixed $value = null): self
    {
        $this->query = $this->query->where($column, $operator, $value);

        return $this;
    }

    /**
     * Add a whereIn clause.
     */
    public function whereIn(string $column, array $values): self
    {
        $this->query = $this->query->whereIn($column, $values);

        return $this;
    }

    /**
     * Add a whereNull clause.
     */
    public function whereNull(string $column): self
    {
        $this->query = $this->query->whereNull($column);

        return $this;
    }

    /**
     * Add a whereNotNull clause.
     */
    public function whereNotNull(string $column): self
    {
        $this->query = $this->query->whereNotNull($column);

        return $this;
    }

    /**
     * Count records.
     */
    public function count(): int
    {
        $result = $this->query->count();
        $this->resetQuery();

        return $result;
    }

    /**
     * Check if a record exists.
     */
    public function exists(string $id): bool
    {
        return $this->query->where($this->model->getKeyName(), $id)->exists();
    }

    /**
     * Get first or create a record.
     */
    public function firstOrCreate(array $attributes, array $values = []): Model
    {
        return $this->model->firstOrCreate($attributes, $values);
    }

    /**
     * Update or create a record.
     */
    public function updateOrCreate(array $attributes, array $values = []): Model
    {
        return $this->model->updateOrCreate($attributes, $values);
    }

    /**
     * Get the underlying query builder.
     */
    public function getQuery(): Builder
    {
        return $this->query;
    }
}
