<?php

namespace CarmineMM\QueryCraft\Data;

use CarmineMM\QueryCraft\Adapter\Sanitizer;
use CarmineMM\QueryCraft\Mapper\Entity;
use CarmineMM\QueryCraft\Mapper\Wrapper;

/**
 * Model class
 * 
 * @package CarmineMM\QueryCraft
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 * @license MIT
 * @version 1.0.0
 */
class Model extends BaseModel
{
    use Timestamps;

    /**
     * Table name
     *
     * @var string
     */
    protected string $table = '';

    /**
     * Hidden fields
     *
     * @var array
     */
    protected array $hidden = [];

    /**
     * Constructor of the model
     */
    /**
     * Get the primary key for the model.
     *
     * @return string
     */
    public function getPrimaryKeyName(): string
    {
        return $this->primaryKey;
    }

    public function __construct(string $connection = 'default')
    {
        if ($this->table === '') {
            $table = explode('\\', get_called_class());
            $this->table = strtolower(
                str_replace('Model', '', array_pop($table))
            ) . 's';
        }

        $this->connection = $connection;

        parent::__construct();
    }

    /**
     * Get the hidden fields
     *
     * @return array
     */
    public function getHiddenFields(): array
    {
        return $this->hidden;
    }

    /**
     * Set hidden fields
     *
     * @param array $hidden
     * @return static
     */
    public function setHidden(array $hidden): static
    {
        $this->hidden = $hidden;
        return $this;
    }

    /**
     * Get the table name
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Set the table name
     *
     * @param string $table
     * @return static
     */
    public function setTable(string $table): static
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Alias for the `get()` method to fetch all records.
     *
     * @param array|null $columns The columns to select.
     * @return array An array of all results.
     *
     * @example
     * // Get all records from the 'logs' table
     * $logs = $db->table('logs')->all();
     */
    public function all(array|null $columns = null): array
    {
        return Wrapper::wrap(
            $this->driver->all($columns === null ? null : Sanitizer::strings($columns)),
            $this
        );
    }

    /**
     * Add a basic where clause to the query.
     *
     * This method supports both two-argument (column, value) and three-argument
     * (column, operator, value) calls. If only two arguments are provided,
     * the operator is assumed to be '='.
     *
     * @param string $column The column name.
     * @param string $operator The comparison operator (e.g., '=', '>', '<', 'LIKE') or the value if using the two-argument version.
     * @param mixed|null $value The value to bind. Required for the three-argument version.
     * @param string $boolean The boolean operator ('AND' or 'OR') to join with the previous clause.
     * @return static The current instance for method chaining.
     *
     * @example
     * // ...->where('id', 1)
     * // Compiles to: WHERE "id" = ? (with 1 as binding)
     *
     * @example
     * // ...->where('status', '!=', 'archived')
     * // Compiles to: WHERE "status" != ? (with 'archived' as binding)
     */
    public function where(string $column, string $sentence, string $three = ''): static
    {
        $column = Sanitizer::string($column);

        if (func_num_args() === 2) {
            $this->driver->where($column, '=', $sentence);
        } else {
            $this->driver->where($column, $sentence, $three);
        }

        return $this;
    }

    /**
     * Adds an "OR WHERE" clause to the query.
     *
     * This method is functionally identical to `where()`, but it joins the clause
     * using the 'OR' boolean operator.
     *
     * @param string $column The column name.
     * @param string $operator The comparison operator or the value.
     * @param mixed|null $value The value to bind.
     * @return static The current instance for method chaining.
     *
     * @example
     * // ...->where('status', 'published')->orWhere('is_featured', true)
     * // Compiles to: WHERE "status" = ? OR "is_featured" = ?
     */
    public function orWhere(string $column, string $sentence, string $three = ''): static
    {
        $column = Sanitizer::string($column);

        if (func_num_args() === 2) {
            $this->driver->orWhere($column, '=', $sentence);
        } else {
            $this->driver->orWhere($column, $sentence, $three);
        }

        return $this;
    }

    /**
     * Adds a "WHERE ... IS NOT NULL" clause to the query.
     *
     * @param string $column The column to check.
     * @param string $boolean The boolean operator ('AND' or 'OR') to join with the previous clause.
     * @return static The current instance for method chaining.
     *
     * @example
     * // ...->whereNotNull('processed_at')
     * // Compiles to: WHERE "processed_at" IS NOT NULL
     */
    public function whereNotNull(string $column): static
    {
        $this->driver->whereNotNull(Sanitizer::string($column));
        return $this;
    }

    /**
     * Adds a "WHERE ... IS NULL" clause to the query.
     *
     * @param string $column The column to check.
     * @param string $boolean The boolean operator ('AND' or 'OR') to join with the previous clause.
     * @return static The current instance for method chaining.
     *
     * @example
     * // ...->whereNull('deleted_at')
     * // Compiles to: WHERE "deleted_at" IS NULL
     */
    /**
     * Adds an ORDER BY clause to the query.
     *
     * @param string $column The column to order by.
     * @param string $direction The direction, 'ASC' or 'DESC'.
     * @return static The current instance for method chaining.
     */
    public function orderBy(string $column, string $direction = 'ASC'): static
    {
        $this->driver->orderBy($column, $direction);
        return $this;
    }

    public function whereNull(string $column): static
    {
        $this->driver->whereNull(Sanitizer::string($column));
        return $this;
    }

    /**
     * View the SQL query
     *
     * @return string
     */
    public function toSql(): string
    {
        return $this->driver->toSql();
    }

    /**
     * Deletes records from the database.
     *
     * If soft deletes are enabled on the model, it will perform a soft delete.
     * Otherwise, it will perform a hard delete.
     *
     * @return array The result of the execution.
     * @throws Exception If trying to perform a bulk delete without explicit permission.
     *
     * @example
     * // Delete user with id 5
     * $db->table('users')->where('id', 5)->delete();
     */
    public function delete(): array
    {
        return $this->driver->delete();
    }

    /**
     * Executes a SELECT query and returns all results.
     *
     * @param array|null $columns The columns to select. If null, uses previously set columns or '*'.
     * @return array An array of results.
     *
     * @example
     * // Get all users
     * $users = $db->table('users')->get();
     *
     * @example
     * // Get specific columns for all users
     * $users = $db->table('users')->get(['id', 'name']);
     */
    public function get(array|null $columns = null): mixed
    {
        return Wrapper::wrap(
            $this->driver->get($columns === null ? null : Sanitizer::strings($columns)),
            $this
        );
    }

    /**
     * Specifies the columns to be selected.
     *
     * @param array $columns An array of column names. Defaults to ['*'].
     * @return static The current instance for method chaining.
     *
     * @example
     * // Select only 'id' and 'email' columns
     * $users = $db->table('users')->select(['id', 'email'])->get();
     */
    public function select(array $columns = ['*']): static
    {
        $this->driver->select(Sanitizer::strings($columns));

        return $this;
    }

    /**
     * Adds a LIMIT and optional OFFSET clause to the query.
     *
     * @param int $limit The maximum number of rows to return.
     * @param int|null $offset The number of rows to skip.
     * @return static The current instance for method chaining.
     *
     * @example
     * // Get 10 users, skipping the first 5
     * $users = $db->table('users')->limit(10, 5)->get();
     */
    public function limit(int $limit, ?int $offset = null): static
    {
        $limit = Sanitizer::integer($limit);

        if ($offset) {
            $offset = Sanitizer::integer($offset);
        }

        $this->driver->limit($limit, $offset);

        return $this;
    }

    /**
     * Executes a SELECT query and returns the first record.
     *
     * @param array $columns The columns to select. Defaults to ['*'].
     * @return mixed The first result record, or null if not found.
     *
     * @example
     * // Get the first user
     * $user = $db->table('users')->first();
     *
     * @example
     * // Get the user with id 1, selecting only id and name
     * $user = $db->table('users')->where('id', 1)->first(['id', 'name']);
     */
    public function first(array $columns = ['*']): mixed
    {
        return $this->driver->first(Sanitizer::strings($columns));
    }

    /**
     * Reset the query
     *
     * @return static
     */
    public function reset(): static
    {
        $this->driver->reset();

        return $this;
    }

    /**
     * Create one element
     *
     * @param array|Entity $values
     * @return array
     */
    public function create(array|Entity $values): array
    {
        return $this->driver->create($values, $this);
    }

    /**
     * Create one element
     *
     * @param array|Entity $values
     * @return static
     */
    public function creator(array|Entity $values): static
    {
        $this->driver->creator($values, $this);

        return $this;
    }

    /**
     * Insert Data Massively
     *
     * @return array
     */
    public function insert(array $data): array
    {
        return $this->driver->insert($data);
    }

    /**
     * Executes a COUNT query.
     *
     * @param string $column The column to count. Defaults to '*'.
     * @return integer The total number of records matching the query.
     *
     * @example
     * // Count all users
     * $total = $db->table('users')->count();
     *
     * @example
     * // Count users with active status
     * $totalActive = $db->table('users')->where('status', 'active')->count();
     */
    public function count(string $column = '*'): int
    {
        return $this->driver->count($column);
    }

    /**
     * Take a snapshot of the current query builder state.
     *
     * @param string|null $name The name of the snapshot.
     * @return static
     */
    public function takeSnapshot(?string $name = null): static
    {
        $this->driver->takeSnapshot($name);
        return $this;
    }

    /**
     * Restore the query builder state from a snapshot.
     *
     * @param string|null $name The name of the snapshot to restore.
     * @return static
     */
    public function restoreSnapshot(?string $name = null): static
    {
        $this->driver->restoreSnapshot($name);
        return $this;
    }
}
