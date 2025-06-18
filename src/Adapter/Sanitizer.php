<?php

namespace CarmineMM\QueryCraft\Adapter;

use CarmineMM\QueryCraft\Facades\DB;

class Sanitizer
{
    /**
     * Sanitizer data strings
     *
     * @param mixed $string
     * @return string
     */
    public static function string(mixed $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES, DB::getSanitizeEncoding());
    }

    /**
     * Sanitizer multiple strings
     *
     * @param array $data
     * @return array
     */
    public static function strings(array $data): array
    {
        return array_map([self::class, 'string'], $data);
    }

    /**
     * Sanitizer integer
     *
     * @param mixed $int
     * @return integer
     */
    public static function integer(mixed $int): int
    {
        try {
            return (int) $int;
        } catch (\Throwable $th) {
            return 0;
        }
    }

    /**
     * Sanitizer float
     *
     * @param mixed $float
     * @return float
     */
    public static function float(mixed $float): float
    {
        try {
            return (float) $float;
        } catch (\Throwable $th) {
            return 0.0;
        }
    }

    /**
     * Sanitizer boolean
     *
     * @param mixed $bool
     * @return boolean
     */
    public static function boolean(mixed $bool): bool
    {
        try {
            return (bool) $bool;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Sanitizer multiple booleans
     *
     * @param array $data
     * @return array
     */
    public static function booleans(array $data): array
    {
        return array_map([self::class, 'boolean'], $data);
    }

    /**
     * Sanitizer multiple integers
     *
     * @param array $data
     * @return array
     */
    public static function integers(array $data): array
    {
        return array_map([self::class, 'integer'], $data);
    }

    /**
     * Sanitizer multiple floats
     *
     * @param array $data
     * @return array
     */
    public static function floats(array $data): array
    {
        return array_map([self::class, 'float'], $data);
    }
}
