<?php

namespace CarmineMM\QueryCraft;

/**
 * Cache only memory, not persistent
 * 
 * @package CarmineMM\QueryCraft
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 * @license MIT
 * @version 1.0.0
 */
class Cache
{
    /**
     * Array cache
     *
     * @var array
     */
    public static $cache = [];

    /**
     * Add in cache
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function add(string $key, mixed $value): void
    {
        self::$cache[$key] = $value;
    }

    /**
     * Get item in cache
     *
     * @param string $key
     * @return mixed
     */
    public static function get(string $key): mixed
    {
        return self::$cache[$key] ?? null;
    }

    /**
     * Get Or create
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public static function remember(string $key, mixed $value): mixed
    {
        return self::$cache[$key] ?? self::$cache[$key] = $value;
    }
}
