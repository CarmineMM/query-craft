<?php

namespace CarmineMM\QueryCraft\Facades;

final class Debug
{
    /**
     * Debug mode status
     *
     * @var boolean
     */
    protected static bool $debug = false;

    /**
     * List query executed
     *
     * @var array
     */
    public static array $queries = [];

    /**
     * Set debug mode
     *
     * @param boolean $set
     * @return void
     */
    public static function debugMode(bool $set = true): void
    {
        self::$debug = $set;
    }

    /**
     * Get debug mode status
     *
     * @return boolean
     */
    public static function getDebugMode(): bool
    {
        return self::$debug;
    }

    /**
     * Add a query to the debug array
     *
     * @param array $query
     * @return void
     */
    public static function addQuery(array $query)
    {
        self::$queries[] = $query;
    }

    /**
     * Get all queries
     * Nomenclature: 
     * <pre>
     * [
     *      [
     *          'query' => 'SELECT * FROM users',
     *          'time' => 0.1,           // Seconds
     *          'memory' => 1024,        // Bytes
     *          'connection' => 'mysql',
     *      ]
     * ]
     * </pre>
     *
     * @return array
     */
    public static function getQueries()
    {
        return self::$queries;
    }

    /**
     * Clear queries
     *
     * @return void
     */
    public static function clearQueries(): void
    {
        self::$queries = [];
    }
}
