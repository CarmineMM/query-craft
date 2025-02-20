<?php

namespace CarmineMM\QueryCraft\Drivers;

use CarmineMM\QueryCraft\Adapter\SQLBaseDriver;
use CarmineMM\QueryCraft\Contracts\Driver;
use CarmineMM\QueryCraft\Data\Model;
use CarmineMM\QueryCraft\Mapper\Wrapper;

final class PostgresSQL extends SQLBaseDriver implements Driver
{
    /**
     * Constructor of the PostgresSQL driver
     */
    public function __construct(
        /**
         * Configuration of the PostgresSQL driver
         *
         * @var array
         */
        private array $config,
        /**
         * Model of the PostgresSQL driver
         *
         * @var Model
         */
        Model $model,
    ) {
        $this->model = $model;

        $port = $this->config['port'] ?? 5432;

        $this->pdo = new \PDO(
            dsn: "pgsql:host={$this->config['host']};port={$port};dbname={$this->config['database']}",
            username: $this->config['username'],
            password: $this->config['password'],
            options: $this->config['options'] ?? []
        );
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
}
