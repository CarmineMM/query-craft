<?php

namespace CarmineMM\QueryCraft\Adapter;

use CarmineMM\QueryCraft\Data\Model;
use CarmineMM\QueryCraft\Data\CarryOut;

abstract class SQLBaseDriver extends CarryOut
{
    /**
     * Layouts para las query's
     *
     * @var array
     */
    protected array $layout = [
        'select' => 'SELECT {column} {innerQuery} FROM {table} {where} {group} {order} {limit} {offset}',
        'insert' => 'INSERT INTO {table} ({keys}) VALUES ({values})',
        //'update' => 'UPDATE %s SET %s',
        'delete' => 'DELETE FROM {table} {where}',
    ];

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
     * @return mixed
     */
    public function get(array $columns = ['*']): mixed
    {
        $this->instance('select');

        $this->sql = str_replace('{column}', implode(', ', $columns), $this->sql);

        return $this->exec();
    }
}
