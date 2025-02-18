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
}
