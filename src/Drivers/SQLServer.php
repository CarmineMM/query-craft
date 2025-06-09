<?php

namespace CarmineMM\QueryCraft\Drivers;

use CarmineMM\QueryCraft\Adapter\SQLBaseDriver;
use CarmineMM\QueryCraft\Cache;
use CarmineMM\QueryCraft\Connection;
use CarmineMM\QueryCraft\Contracts\Driver;
use CarmineMM\QueryCraft\Data\Model;
use Exception;

final class SQLServer extends SQLBaseDriver implements Driver
{
    /**
     * Constructor of the SQLServer driver
     */
    public function __construct(
        /**
         * Configuration of the SQLServer driver
         *
         * @var array
         */
        array $config,
        /**
         * Model of the SQLServer driver
         *
         * @var Model
         */
        Model $model,
    ) {
        $this->model = $model;

        if ($this->model->getSchema() === '') {
            $this->model->setSchema($config['schema'] ?? 'dbo');
        }


        $port = $config['port'] ?? 1433;

        $this->pdo = Cache::remember(
            key: "{$config['host']}:{$port}-{$config['database']}-{$config['username']}",
            value: new \PDO(
                dsn: "sqlsrv:Server={$config['host']},{$port};Database={$config['database']}",
                username: $config['username'],
                password: $config['password'],
                options: $config['options'] ?? null
            ),
            active: Connection::$instance->cache
        );
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
                ? "[{$this->model->getSchema()}].[{$this->model->getTable()}]"
                : "[$this->model->getTable()]";

            $this->sql = str_replace('{table}', $table, $this->layout[$type]);
        }

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
            ? str_replace(
                ['{limit}', '{offset}', '{order}'],
                [
                    "OFFSET {$offset} ROWS FETCH NEXT {$limit} ROWS ONLY", // <-- {limit}
                    '',                                                    // <-- {offset}
                    '{order} ORDER BY (SELECT NULL)'                       // <-- {order}
                ],
                $this->sql
            )
            : str_replace('{column}', "TOP({$limit}) {column}", $this->sql);

        return $this;
    }
}
