<?php

namespace CarmineMM\QueryCraft\Adapter;

use CarmineMM\QueryCraft\Data\Model;
use CarmineMM\QueryCraft\Data\CarryOut;

abstract class SQLBaseDriver extends CarryOut
{
    /**
     * Prepara la instancia del SQL
     *
     * @param string $type
     * @return static
     */
    protected function instance(string $type = ''): static
    {
        $this->sql = str_replace('{table}', $this->model->getTable(), $this->layout[$type]);

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
     * Where clause for the query
     *
     * @param string $column
     * @param string $sentence
     * @param string $three
     * @return static
     */
    public function where(string $column, string $sentence, string $three = ''): static
    {
        foreach ($this->layout as $key => $value) {
            if (str_contains($value, 'WHERE')) {
                $this->layout[$key] = $three
                    ? str_replace('{where}', "AND {$column} {$sentence} {$three} {where}", $value)
                    : str_replace('{where}', "AND {$column} = '{$sentence}' {where}", $value);
            } else {
                $this->layout[$key] = $three
                    ? str_replace('{where}', "WHERE {$column} {$sentence} {$three} {where}", $value)
                    : str_replace('{where}', "WHERE {$column} = '{$sentence}' {where}", $value);
            }
        }

        return $this;
    }

    /**
     * Where clause for the query
     *
     * @param string $column
     * @param string $sentence
     * @param string $three
     * @return static
     */
    public function orWhere(string $column, string $sentence, string $three = ''): static
    {
        foreach ($this->layout as $key => $value) {
            if (str_contains($value, 'WHERE')) {
                $this->layout[$key] = $three
                    ? str_replace('{where}', "OR {$column} {$sentence} {$three} {where}", $value)
                    : str_replace('{where}', "OR {$column} = '{$sentence}' {where}", $value);
            } else {
                $this->layout[$key] = $three
                    ? str_replace('{where}', "OR WHERE {$column} {$sentence} {$three} {where}", $value)
                    : str_replace('{where}', "OR WHERE {$column} = '{$sentence}' {where}", $value);
            }
        }

        return $this;
    }

    /**
     * Get elements of the table
     *
     * @param array $columns
     * @return array
     */
    public function get(array $columns = ['*']): array
    {
        $this->instance('select');

        $this->sql = str_replace('{column}', implode(', ', $columns), $this->sql);

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
        $this->instance($sentence);

        $this->prepareSql();

        return $this->sql;
    }

    /**
     * All elements of the table
     *
     * @param array $columns
     * @return array
     */
    public function all(array $columns = ['*']): array
    {
        return $this->setColumns($columns)->instance('select')->exec();
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
        $this->sql = $offset
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
}
