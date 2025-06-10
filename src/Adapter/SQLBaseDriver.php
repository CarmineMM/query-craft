<?php

namespace CarmineMM\QueryCraft\Adapter;

use CarmineMM\QueryCraft\Data\Model;
use CarmineMM\QueryCraft\Data\CarryOut;
use CarmineMM\QueryCraft\Mapper\Entity;
use CarmineMM\QueryCraft\Mapper\Modeling;
use Exception;
use InvalidArgumentException;

/**
 * Drivers base para casi todas las bases de datos basadas en SQL
 *
 * @package CarmineMM\QueryCraft
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 * @version 1.5.0
 */
abstract class SQLBaseDriver extends CarryOut
{
    /**
     * Prepares the SQL query by compiling all its parts.
     *
     * @return static
     */
    protected function prepareSql(): static
    {
        if ($this->model->getSoftDeletedAtField() !== null) {
            $this->whereNull($this->model->getSoftDeletedAtField());
        }

        // Replace {column} placeholder with the selected columns
        $columns = array_map([$this, 'addQuotes'], $this->columns);
        $this->sql = str_replace('{column}', implode(', ', $columns), $this->sql);

        $this->compileWheres();

        // Remove any unused placeholders
        $this->cleanupSql();

        return $this;
    }

    /**
     * Cleans up the final SQL query by removing any unused placeholders.
     *
     * This prevents SQL syntax errors from placeholders that were not replaced,
     * such as `{innerQuery}` or `{group}` if they weren't used.
     */
    protected function cleanupSql(): void
    {
        // This regex will find all {placeholders} like {innerQuery}, {group}, etc. and remove them
        $this->sql = preg_replace('/\{\w+\}/', '', $this->sql);
        // Also remove any resulting double spaces and trim whitespace
        $this->sql = trim(preg_replace('/\s\s+/', ' ', $this->sql));
    }

    /**
     * Compiles the stored WHERE clauses into a single SQL string.
     *
     * It constructs the `WHERE` part of the query from the `$this->wheres` array,
     * ensuring correct boolean logic and placeholders.
     */
    protected function compileWheres(): void
    {
        if (empty($this->wheres)) {
            $this->sql = str_replace('{where}', '', $this->sql);
            return;
        }

        $sql = '';
        foreach ($this->wheres as $i => $where) {
            $clause = trim("{$where['column']} {$where['operator']} {$where['placeholder']}");

            if ($i > 0) {
                $sql .= " {$where['boolean']} ";
            }

            $sql .= $clause;
        }

        $this->sql = str_replace('{where}', "WHERE {$sql}", $this->sql);
    }

    /**
     * Quotes an identifier for use in a query.
     *
     * This method handles simple and qualified identifiers (e.g., "column", "table.column")
     * by wrapping each part in double quotes, which is the standard SQL way to quote identifiers.
     * It prevents conflicts with SQL keywords and preserves case sensitivity.
     * Drivers for specific databases (like MySQL, which uses backticks) can override this.
     *
     * @param string $identifier The identifier to quote.
     * @return string The quoted identifier.
     *
     * @example
     * // Returns "my_column"
     * $this->addQuotes('my_column');
     *
     * @example
     * // Returns "my_schema"."my_table"
     * $this->addQuotes('my_schema.my_table');
     */
    protected function addQuotes(string $identifier): string
    {
        if ($identifier === '*') {
            return $identifier;
        }

        $parts = explode('.', $identifier);

        $quotedParts = array_map(function ($part) {
            if ($part === '*') {
                return $part;
            }
            return '"' . str_replace('"', '""', $part) . '"';
        }, $parts);

        return implode('.', $quotedParts);
    }

    /**
     * Prepara la instancia del SQL
     *
     * @param string $type
     * @return static
     */
    protected function instance(string $type = ''): static
    {
        if ($this->model->getTable() === '') {
            throw new Exception("Table name is required!", 500);
        }

        if ($this->sql === '') {
            $table = $this->model->getSchema()
                ? $this->addQuotes($this->model->getSchema()) . '.' . $this->addQuotes($this->model->getTable())
                : $this->addQuotes($this->model->getTable());

            $this->sql = str_replace('{table}', $table, $this->layout[$type]);
        }

        return $this;
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
    public function where(string $column, string $operator, mixed $value = null, string $boolean = 'AND'): static
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'column'      => $this->addQuotes($column),
            'operator'    => $operator,
            'boolean'     => $boolean,
            'placeholder' => '?',
        ];

        $this->addBinding($value);

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
    public function orWhere(string $column, string $operator, mixed $value = null): static
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        return $this->where($column, $operator, $value, 'OR');
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
    public function whereNotNull(string $column, string $boolean = 'AND'): static
    {
        $this->wheres[] = [
            'column'      => $this->addQuotes($column),
            'operator'    => 'IS NOT NULL',
            'boolean'     => $boolean,
            'placeholder' => '',
        ];

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
    public function whereNull(string $column, string $boolean = 'AND'): static
    {
        $this->wheres[] = [
            'column'      => $this->addQuotes($column),
            'operator'    => 'IS NULL',
            'boolean'     => $boolean,
            'placeholder' => '',
        ];

        return $this;
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
    public function get(array|null $columns = null): array
    {
        if ($columns) {
            $this->select($columns);
        }

        $this->instance('select');

        return $this->exec();
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
        return $this->get($columns);
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
        $this->columns = $columns;

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
        $this->instance('select');

        $this->sql = str_replace('{limit}', "LIMIT {$limit}", $this->sql);

        if ($offset !== null) {
            $this->sql = str_replace('{offset}', "OFFSET {$offset}", $this->sql);
        }

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
        $this->limit(1);
        $result = $this->get($columns);

        return $result[0] ?? null;
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
        $columnToCount = $column === '*' ? '*' : $this->addQuotes($column);
        $this->select(["COUNT({$columnToCount}) as aggregate"]);
        $this->instance('select');
        $result = $this->exec();

        return (int) ($result[0]['aggregate'] ?? 0);
    }

    /**
     * Returns the generated SQL query as a string.
     *
     * @param string $sentence The type of query to build ('select', 'insert', etc.).
     * @return string The generated SQL query.
     *
     * @example
     * $sql = $db->table('users')->where('id', 1)->toSql();
     * "SELECT * FROM \"users\" WHERE \"id\" = ?"
     */
    public function toSql($sentence = 'select'): string
    {
        $this->instance($sentence);
        $this->prepareSql();
        $sql = $this->sql;
        $this->reset();

        return $sql;
    }

    /**
     * Prepares a bulk insert operation.
     *
     * This method is for inserting a large number of records efficiently. It does not
     * trigger model events, timestamps, or other model-specific logic.
     *
     * @param array $data An array of associative arrays, where each inner array represents a row.
     * @return array The result of the execution.
     * @throws InvalidArgumentException If the data is not structured correctly.
     *
     * @example
     * $users = [
     *     ['name' => 'John', 'email' => 'john@example.com'],
     *     ['name' => 'Jane', 'email' => 'jane@example.com'],
     * ];
     * $db->table('users')->insert($users);
     */
    public function insert(array $data): array
    {
        if (empty($data)) {
            return [];
        }

        $this->instance('insert');

        $columns = array_keys(reset($data));
        $quotedColumns = implode(', ', array_map([$this, 'addQuotes'], $columns));

        $valuePlaceholders = [];
        $allParams = [];
        foreach ($data as $item) {
            if (!is_array($item) || array_keys($item) !== $columns) {
                throw new InvalidArgumentException("All items in \$data must have the same keys as the first item.");
            }

            $placeholdersRow = implode(', ', array_fill(0, count($columns), '?'));
            $valuePlaceholders[] = "({$placeholdersRow})";

            foreach ($columns as $column) {
                $allParams[] = $item[$column] ?? null;
            }
        }

        $this->sql = str_replace('{keys}', $quotedColumns, $this->sql);
        $this->sql = str_replace('({values})', implode(', ', $valuePlaceholders), $this->sql);

        return $this->unsafeExec($allParams);
    }

    /**
     * Prepares an insert statement for a single record, processing it through the model.
     *
     * @param array|Entity $values The values to insert.
     * @param Model $model The model instance for processing data.
     * @return static
     */
    public function creator(array|Entity $values, Model $model): static
    {
        $this->instance('insert');
        $values = $values instanceof Entity ? $values->toArray() : $values;

        $fillable_data = Modeling::fillableData($model, $values);
        ['values' => $insertValues] = Modeling::applyTimeStamps($model, $fillable_data);

        $keys = [];
        $placeholder = [];
        foreach ($insertValues as $key => $value) {
            $keys[] = $this->addQuotes($key);
            $placeholder[] = '?';
            $this->addBinding($value);
        }

        $this->sql = str_replace('{keys}', implode(', ', $keys), $this->sql);
        $this->sql = str_replace('{values}', implode(', ', $placeholder), $this->sql);

        return $this;
    }

    /**
     * Creates a new record in the database.
     *
     * @param array|Entity $values The data to create.
     * @param Model $model The model instance.
     * @return array The result of the execution.
     *
     * @example
     * $newUser = ['name' => 'Peter', 'email' => 'peter@example.com'];
     * $db->table('users')->create($newUser, new User());
     */
    public function create(array|Entity $values, Model $model): array
    {
        return $this->creator($values, $model)->exec();
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
        if (empty($this->wheres) && !$this->model->allow_bulk_delete) {
            throw new Exception("Your delete doesn't have a Where! 😢 Bulk deletes are not allowed by default.", 500);
        }

        if ($this->model->getSoftDeletedAtField() !== null) {
            return $this->update([
                $this->model->getSoftDeletedAtField() => date('Y-m-d H:i:s')
            ], $this->model);
        }

        $this->instance('delete');

        return $this->exec();
    }

    /**
     * Updates records in the database.
     *
     * @param array|Entity $values The new values.
     * @param Model $model The model instance.
     * @return array The result of the execution.
     * @throws Exception If trying to perform a bulk update without a WHERE clause.
     *
     * @example
     * // Update user with id 1
     * $db->table('users')->where('id', 1)->update(['status' => 'inactive'], new User());
     */
    public function update(array|Entity $values, Model $model): array
    {
        if (empty($this->wheres) && !$model->allow_bulk_update) {
            throw new Exception("Your update doesn't have a Where clause! 😢 Bulk updates are not allowed by default.", 500);
        }

        $this->instance('update');

        $values = $values instanceof Entity ? $values->toArray() : $values;
        $fillable_data = Modeling::fillableData($model, $values);
        $update_data = Modeling::applyUpdatedAt($model, $fillable_data)['values'];

        $setClauses = [];
        $updateBindings = [];
        foreach ($update_data as $key => $value) {
            $setClauses[] = "{$this->addQuotes($key)} = ?";
            $updateBindings[] = $value;
        }

        $this->bindings = array_merge($updateBindings, $this->bindings);

        $setString = implode(', ', $setClauses);
        $this->sql = str_replace('{set}', $setString, $this->sql);

        return $this->exec();
    }
}
