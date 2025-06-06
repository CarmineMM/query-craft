<?php

namespace CarmineMM\QueryCraft;

use CarmineMM\QueryCraft\Contracts\Driver;
use CarmineMM\QueryCraft\Data\Model;
use CarmineMM\QueryCraft\Drivers\MySQL;
use CarmineMM\QueryCraft\Drivers\PostgresSQL;
use PDO;

/**
 * Connection class
 * 
 * @package CarmineMM\QueryCraft
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 * @license MIT
 * @version 1.0.0
 */
final class Connection
{
    /**
     * Listado de conexiones
     *
     * @var array
     */
    private array $connections = [];

    /**
     * Set Debug mode
     *
     * @var boolean
     */
    public bool $debug = false;

    /**
     * Cache queries
     *
     * @var boolean
     */
    public bool $cache = true;

    /**
     * Instancia de la conexión
     *
     * @var Connection|null
     */
    public static ?Connection $instance = null;

    /**
     * Connect to a new database.
     *
     * @param string $name
     * @param array $config
     * @return void
     */
    public static function connect(string $name, array $config): void
    {
        $connections = static::connections();

        $connections[$name] = $config;

        static::$instance->connections = $connections;
    }

    /**
     * Lista de conexiones
     *
     * @return array
     */
    public static function connections(): array
    {
        if (is_null(static::$instance)) {
            static::$instance = new self;
        }

        return static::$instance->connections;
    }

    /**
     * Mode debug mode
     *
     * @param boolean $set
     * @return void
     */
    public static function debugMode(bool $set = true): void
    {
        if (is_null(static::$instance)) {
            static::$instance = new self;
        }

        static::$instance->debug = $set;
    }

    /**
     * Deactivate cache in queries
     *
     * @param boolean $useCache
     * @return void
     */
    public static function disabledCache(bool $useCache = false): void
    {
        if (is_null(static::$instance)) {
            static::$instance = new self;
        }

        static::$instance->cache = $useCache;
    }

    /**
     * Connect by PDO using a connection name.
     *
     * @param string $connection
     * @return mixed
     */
    public static function pdo(
        string $connection,
        Model $model = null,
    ): mixed {
        $connections = Connection::connections();

        $config = $connections[$connection] ?? null;
        $connection = null;

        if (is_null($config)) {
            throw new \Exception('Connection not found', 500);
        }

        $customDriver = $config['custom_driver'] ?? null;

        if (is_string($customDriver)) {
            $connection = new $customDriver($config, $model);
        } else {
            $connection = match ($config['driver']) {
                'pgsql' => new PostgresSQL($config, $model),
                'mysql', 'mariadb' => new MySQL($config, $model),
                default => throw new \Exception('Driver not found', 500),
            };
        }

        return $connection;
    }
}
