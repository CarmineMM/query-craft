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
            $this->whereNotNull($this->model->getSoftDeletedAtField());
        }

        // Replace {column} placeholder with the selected columns
        $this->sql = str_replace('{column}', implode(', ', $this->columns), $this->sql);

        $this->compileWheres();

        // Remove any unused placeholders
        $this->cleanupSql();

        return $this;
    }

    /**
     * Cleans up the SQL query by removing unused placeholders.
     */
    protected function cleanupSql(): void
    {
        // This regex will find all {placeholders} like {innerQuery}, {group}, etc. and remove them
        $this->sql = preg_replace('/\{\w+\}/', '', $this->sql);
        // Also remove any resulting double spaces and trim whitespace
        $this->sql = trim(preg_replace('/\s\s+/', ' ', $this->sql));
    }

    /**
     * Compiles the where clauses into a single SQL string.
     */
    protected function compileWheres(): void
    {
        if (empty($this->wheres)) {
            $this->sql = str_replace('{where}', '', $this->sql);
            return;
        }

        $whereClauses = [];
        foreach ($this->wheres as $where) {
            $whereClauses[] = "{$where['boolean']} {$where['column']} {$where['operator']} {$where['placeholder']}";
        }

        // Remove the initial 'AND ' or 'OR ' from the first clause
        $sql = ltrim(implode(' ', $whereClauses), ' ANDOR');

        $this->sql = str_replace('{where}', "WHERE {$sql}", $this->sql);
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
                ? "{$this->model->getSchema()}.{$this->model->getTable()}"
                : $this->model->getTable();

            $this->sql = str_replace('{table}', $table, $this->layout[$type]);
        }

        return $this;
    }

    /**
     * Execute a manually query
     *
     * @param string $query
     * @return mixed The return can be conditioned to the required query
     */
    public function query(string $query): mixed
    {
        $this->sql = $query;

        return $this->exec();
    }

    /**
     * Add a basic where clause to the query.
     *
     * @param string $column
     * @param string $operator
     * @param mixed  $value
     * @param string $boolean
     * @return static
     */
    public function where(string $column, string $operator, mixed $value = null, string $boolean = 'AND'): static
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'column'      => $column,
            'operator'    => $operator,
            'value'       => $value,
            'boolean'     => $boolean,
            'placeholder' => '?',
        ];

        $this->bindings[] = $value;

        return $this;
    }

    /**
     * Add an "or where" clause to the query.
     *
     * @param  string  $column
     * @param  string  $operator
     * @param  mixed   $value
     * @return static
     */
    public function orWhere(string $column, string $operator, mixed $value = null): static
    {
        return $this->where($column, $operator, $value, 'OR');
    }

    /**
     * Where clause for the query
     *
     * @param string $column
     * @return static
     */
    /**
     * Add a "where not null" clause to the query.
     *
     * @param  string  $column
     * @param  string  $boolean
     * @return static
     */
    public function whereNotNull(string $column, string $boolean = 'AND'): static
    {
        $this->wheres[] = [
            'column'      => $column,
            'operator'    => 'IS NOT NULL',
            'value'       => null,
            'boolean'     => $boolean,
            'placeholder' => '',
        ];

        return $this;
    }

    /**
     * Where clause for the query
     *
     * @param string $column
     * @param string $boolean
     * @return static
     */
    public function whereNull(string $column, string $boolean = 'AND'): static
    {
        $this->wheres[] = [
            'column'      => $column,
            'operator'    => 'IS NULL',
            'value'       => null,
            'boolean'     => $boolean,
            'placeholder' => '',
        ];

        return $this;
    }

    /**
     * Get elements of the table
     *
     * @param array|null $columns
     * @return array
     */
    public function get(array|null $columns = null): array
    {
        $this->instance('select');

        $this->sql = str_replace('{column}', implode(', ', $columns ?? $this->columns), $this->sql);

        return $this->exec();
    }

    /**
     * View the SQL query
     *
     * @param string $sentence ['select', 'insert', 'update', 'delete']
     * @return string
     */
    public function toSql($sentence = 'select'): string
    {
        if ($this->sql === '') {
            $this->instance($sentence);
        }

        $this->prepareSql();

        return $this->sql;
    }

    /**
     * All elements of the table
     *
     * @param array|null $columns
     * @return array
     */
    public function all(array|null $columns = null): array
    {
        return $this->setColumns($columns)->instance('select')->exec();
    }

    /**
     * Select instance
     *
     * @param array $columns
     * @return static
     */
    public function select(array $columns = ['*']): static
    {
        $this->instance('select')->setColumns($columns);

        return $this;
    }

    /**
     * Limit and offset for the query
     *
     * @param integer $limit
     * @param integer|null $offset
     * @return static
     */
    public function limit(int $limit, ?int $offset = null): static
    {
        $this->instance('select');

        $this->sql = !is_null($offset)
            ? str_replace('{limit}', "LIMIT {$limit} OFFSET {$offset}", $this->sql)
            : str_replace('{limit}', "LIMIT {$limit}", $this->sql);

        return $this;
    }

    /**
     * First element of the table,
     * and limit 1 in array
     *
     * @param array $column
     * @return mixed
     */
    public function first(array $column = ['*']): mixed
    {
        return $this
            ->setColumns($column)
            ->instance('select')
            ->limit(1)
            ->exec()[0] ?? null;
    }

    /**
     * Countable elements
     *
     * @param string $column
     * @return int
     */
    public function count(string $column = '*'): int
    {
        $this->instance('select');

        $this->sql = str_replace('{column}', "COUNT({$column})", $this->sql);

        return (int) reset($this->exec()[0]);
    }

    /**
     * Create a new element in BD,
     * Using the fillable fields
     *
     * @param array $values
     * @return static
     */
    public function creator(array|Entity $values, Model $model): static
    {
        $this->instance('insert');
        $values = $values instanceof Entity ? $values->toArray() : $values;

        $fillable_data = Modeling::fillableData($model, $values);

        ['values' => $insertValues] = Modeling::applyTimeStamps($model, $fillable_data);

        $placeholder = [];
        $keys = [];

        foreach ($insertValues as $key => $value) {
            $keys[] = $key;
            $placeholder[] = ':' . $key;
            $this->data[$key] = Sanitizer::string($value);
        }

        $this->sql = strtr($this->sql, [
            '{keys}' => implode(', ', $keys),
            // Set Placeholders
            '{values}' => implode(',', $placeholder),
        ]);

        return $this;
    }

    /**
     * Insert large -scale data, do not make mappers or timestamp
     *
     * @param array $data
     * @return array
     */
    public function insert(array $data): array
    {
        $this->instance('insert');

        $placeholder = [];
        $columns = array_keys(reset($data));

        foreach ($data as $item) {
            // Aseguramos que cada item sea un array y tenga las mismas claves que el primero
            if (!is_array($item) || array_keys($item) !== $columns) {
                // Los items mandados no corresponden con el formato esperado
                throw new InvalidArgumentException("All items in \$data must have the same keys as the first item.");
            }

            $placeholdersRow = implode(', ', array_fill(0, count($columns), '?')); // Genera '?, ?, ?'
            $valuePlaceholders[] = "({$placeholdersRow})"; // Genera '(?, ?, ?)'

            // Recopila los valores para los parámetros de PDO
            foreach ($columns as $column) {
                $allParams[] = $item[$column] ?? null;
            }
        }

        $this->instance('insert');

        $this->sql = strtr($this->sql, [
            '{keys}' => implode(', ', $columns),
            // Set Placeholders
            '({values})' => implode(',', $valuePlaceholders),
        ]);

        return $this->unsafeExec($allParams);
    }

    /**
     * Create a new element in BD
     *
     * @param array|Entity $values
     * @param Model $model
     * @return array
     */
    public function create(array|Entity $values, Model $model): array
    {
        return $this->creator($values, $model)->exec($this->data);
    }

    /**
     * Delete element
     *
     * @return array
     */
    public function delete(): array
    {
        if (empty($this->wheres) && !$this->model->allow_bulk_delete) {
            // You are launching a DELETE on a table without first delimiting by a 'WHERE'.
            // It is not necessary to clarify that this could delete all the data in your table.
            // If you want to allow this action, enable allow_bulk_delete in your model or DB::allowBulkDelete().
            throw new Exception("Your delete doesn't have a Where! 😢", 500);
        }

        // Handle soft deletes
        if ($this->model->getSoftDeletedAtField() !== null) {
            return $this->update([
                $this->model->getSoftDeletedAtField() => date('Y-m-d H:i:s')
            ], $this->model);
        }

        $this->instance('delete');

        return $this->exec();
    }

    /**
     * Update element
     *
     * @param array|Entity $values
     * @param Model $model
     * @return array
     */
    public function update(array|Entity $values, Model $model): array
    {
        if (empty($this->wheres) && !$model->allow_bulk_update) {
            throw new Exception("Your update doesn't have a Where clause! 😢 Bulk updates are not allowed by default. Enable allow_bulk_update in your model or add a where condition.", 500);
        }

        $this->instance('update');

        $values = $values instanceof Entity ? $values->toArray() : $values;
        $fillable_data = Modeling::fillableData($model, $values);
        $update_data = Modeling::applyUpdatedAt($model, $fillable_data)['values'];

        // Build the SET clause
        $setClauses = [];
        $updateBindings = [];
        foreach ($update_data as $key => $value) {
            $setClauses[] = "{$key} = ?";
            $updateBindings[] = $value;
        }
        
        // Prepend update bindings to the main bindings array to ensure they come first
        $this->bindings = array_merge($updateBindings, $this->bindings);

        $setString = implode(', ', $setClauses);

        $this->sql = str_replace('{set}', $setString, $this->sql);

        return $this->exec();
    }
}
