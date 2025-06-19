<?php

namespace CarmineMM\QueryCraft\Contracts;

use CarmineMM\QueryCraft\Data\Model;
use CarmineMM\QueryCraft\Mapper\Entity;

/**
 * Driver interface
 * 
 * @package CarmineMM\QueryCraft\Contracts
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 * @license MIT
 * @version 1.0.0
 */
interface Driver
{
    /**
     * Execute the query as a "select" statement.
     *
     * @param array $columns The columns to select. Defaults to ['*']
     * @return array The query results
     */
    public function all(array $columns = ['*']);

    /**
     * Add a basic where clause to the query.
     *
     * @param string $column The column to compare
     * @param string $operator The comparison operator (e.g., '=', '>', '<', 'LIKE')
     * @param mixed $value The value to compare against
     * @return static
     */
    public function where(string $column, string $operator, $value = null): static;

    /**
     * Add an "or where" clause to the query.
     *
     * @param string $column The column to compare
     * @param string $operator The comparison operator (e.g., '=', '>', '<', 'LIKE')
     * @param mixed $value The value to compare against
     * @return static
     */
    public function orWhere(string $column, string $operator, $value = null): static;

    /**
     * Add a "where not null" clause to the query.
     *
     * @param string $column The column to check
     * @return static
     */
    public function whereNotNull(string $column): static;

    /**
     * Add a "where null" clause to the query.
     *
     * @param string $column The column to check
     * @return static
     */
    public function whereNull(string $column): static;

    /**
     * Get the SQL representation of the query.
     *
     * @param string $sentence The type of query (e.g., 'select', 'insert', 'update', 'delete')
     * @return string The generated SQL query
     */
    public function toSql(string $sentence = 'select'): string;

    /**
     * Execute the query as a "select" statement and return the result.
     *
     * @param array $columns The columns to select
     * @return array The query results
     */
    public function get(array $columns = ['*']): array;

    /**
     * Set the "limit" and "offset" for the query.
     *
     * @param int $limit The maximum number of records to return
     * @param int|null $offset The number of records to skip
     * @return static
     */
    public function limit(int $limit, ?int $offset = null): static;

    /**
     * Execute the query and get the first result.
     *
     * @param array $columns The columns to select
     * @return mixed The first result or null if no results
     */
    public function first(array $columns = ['*']): mixed;

    /**
     * Insert a new record into the database.
     *
     * @param array $data The data to insert
     * @return array The result of the insert operation
     */
    public function insert(array $data): array;

    /**
     * Delete records from the database.
     *
     * @return array The result of the delete operation
     */
    public function delete(): array;

    /**
     * Set the columns to be selected.
     *
     * @param array $columns The columns to select
     * @return static
     */
    public function select(array $columns = ['*']): static;

    /**
     * Prepare a new model instance with the given values.
     *
     * @param array|Entity $values The values to set on the model
     * @param Model $model The model instance
     * @return static
     */
    public function creator(array|Entity $values, Model $model): static;

    /**
     * Save a new model and return the instance.
     *
     * @param array|Entity $values The values to set on the model
     * @param Model $model The model instance
     * @return array The created model data
     */
    public function create(array|Entity $values, Model $model): array;

    /**
     * Reset the query to its initial state.
     *
     * @return static
     */
    public function reset(): static;

    /**
     * Retrieve the "count" result of the query.
     *
     * @param string $column The column to count
     * @return int The number of records
     */
    public function count(string $column = '*'): int;

    /**
     * Take a snapshot of the current query state.
     *
     * @param string|null $name The name of the snapshot
     * @return static
     */
    public function takeSnapshot(?string $name = null): static;

    /**
     * Restore a previously taken snapshot of the query state.
     *
     * @param string|null $name The name of the snapshot to restore
     * @return static
     */
    public function restoreSnapshot(?string $name = null): static;

    /**
     * Add an "order by" clause to the query.
     *
     * @param string $column The column to order by
     * @param string $direction The direction to order by (ASC or DESC)
     * @return static
     */
    public function orderBy(string $column, string $direction = 'ASC'): static;

    /**
     * Execute a raw SQL statement.
     *
     * @param string $query The SQL query to execute
     * @return bool True on success, false on failure
     */
    public function statement(string $query): bool;

    /**
     * Truncate a table.
     *
     * @param string $table The name of the table to truncate
     * @return void
     */
    public function truncate(string $table): void;

    /**
     * Get the PDO connection instance.
     *
     * @return \PDO The PDO connection instance
     */
    public function getPdo(): \PDO;
}
