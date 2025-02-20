<?php

namespace CarmineMM\QueryCraft\Drivers;

use CarmineMM\QueryCraft\Adapter\SQLBaseDriver;
use CarmineMM\QueryCraft\Contracts\Driver;
use CarmineMM\QueryCraft\Data\Model;

/**
 * MySQL driver
 * 
 * @package CarmineMM\QueryCraft
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 * @license MIT
 * @version 1.0.0
 */
final class MySQL extends SQLBaseDriver implements Driver
{
    /**
     * Instance of the MySQL driver
     *
     * @var MySQL|null
     */
    public static ?MySQL $instance = null;

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

        $port = $config['port'] ?? 3306;

        $this->pdo = new \PDO(
            dsn: "mysql:host={$config['host']};port={$port};dbname={$config['database']}",
            username: $config['username'],
            password: $config['password'],
            options: $config['options'] ?? []
        );
    }
}
