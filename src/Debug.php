<?php

namespace CarmineMM\QueryCraft;

final class Debug
{
    public static array $queries = [];

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
     * Nomenclatura: [
     *      [
     *          'query' => 'SELECT * FROM users',
     *          'time' => 0.1,           // Seconds
     *          'memory' => 1024,        // Bytes
     *          'connection' => 'mysql',
     *      ]
     * ]
     *
     * @return array
     */
    public static function getQueries()
    {
        return self::$queries;
    }

    public static function clearQueries()
    {
        self::$queries = [];
    }
}
