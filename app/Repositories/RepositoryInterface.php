<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface RepositoryInterface
{
    /**
     * Get all records.
     */
    public function all(array $columns = ['*']): Collection;

    /**
     * Get paginated records.
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    /**
     * Find a record by ID.
     */
    public function find(string $id, array $columns = ['*']): ?Model;

    /**
     * Find a record by ID or fail.
     */
    public function findOrFail(string $id, array $columns = ['*']): Model;

    /**
     * Find records by a field.
     */
    public function findBy(string $field, mixed $value, array $columns = ['*']): Collection;

    /**
     * Find first record by a field.
     */
    public function findFirstBy(string $field, mixed $value, array $columns = ['*']): ?Model;

    /**
     * Create a new record.
     */
    public function create(array $data): Model;

    /**
     * Update a record.
     */
    public function update(string $id, array $data): bool;

    /**
     * Delete a record.
     */
    public function delete(string $id): bool;

    /**
     * Get records with relationships.
     */
    public function with(array $relations): self;

    /**
     * Order records by a column.
     */
    public function orderBy(string $column, string $direction = 'asc'): self;

    /**
     * Add a where clause.
     */
    public function where(string $column, mixed $operator, mixed $value = null): self;

    /**
     * Count records.
     */
    public function count(): int;
}
