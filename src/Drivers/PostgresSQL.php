<?php

namespace CarmineMM\QueryCraft\Drivers;

use CarmineMM\QueryCraft\Adapter\SQLBaseDriver;
use CarmineMM\QueryCraft\Facades\Cache;
use CarmineMM\QueryCraft\Facades\Connection;
use CarmineMM\QueryCraft\Contracts\Driver;
use CarmineMM\QueryCraft\Data\Model;

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
        array $config,
        /**
         * Model of the PostgresSQL driver
         *
         * @var Model
         */
        Model $model,
    ) {
        $this->model = $model;

        if ($this->model->getSchema() === '' && isset($config['schema'])) {
            $this->model->setSchema($config['schema']);
        }

        $port = $config['port'] ?? 5432;

        $this->pdo = Cache::remember(
            key: "{$config['host']}:{$port}-{$config['database']}-{$config['username']}",
            value: new \PDO(
                dsn: "pgsql:host={$config['host']};port={$port};dbname={$config['database']}",
                username: $config['username'],
                password: $config['password'],
                options: $config['options'] ?? null
            ),
            active: Connection::$instance->cache
        );
    }
}
