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
    public static function set(string $key, mixed $value): void
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
     * Check item in cache
     *
     * @param string $key
     * @return boolean
     */
    public static function has(string $key): bool
    {
        return isset(self::$cache[$key]);
    }

    /**
     * Get Or create
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public static function remember(string $key, mixed $value, bool $active = true): mixed
    {
        return !$active
            ? null
            : (self::$cache[$key] ?? self::$cache[$key] = $value);
    }

    /**
     * Invalidate by table find, based in cache
     *
     * @param string $table
     * @return array
     */
    public static function invalidateByTable(string $table): array
    {
        $finds = [];

        foreach (self::$cache as $key => $value) {
            if (str_contains($key, " FROM {$table} ")) {
                unset(self::$cache[$key]);
                $finds[] = $key;
            }
        }

        return $finds;
    }
}
