<?php

namespace CarmineMM\QueryCraft\Adapter;

use CarmineMM\QueryCraft\DB;

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
     * @param integer $int
     * @return integer
     */
    public static function integer(int $int): int
    {
        return (int) $int;
    }
}
