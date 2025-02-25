<?php

namespace CarmineMM\QueryCraft;

use CarmineMM\QueryCraft\Contracts\Driver;

/**
 * DB class
 * 
 * @package CarmineMM\QueryCraft
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 * @license MIT
 * @version 1.0.0
 */
final class DB
{
    /**
     * @var Driver
     */
    private Driver $driver;

    /**
     * Timezone
     *
     * @var string
     */
    protected static string $timezone = 'UTC';

    /**
     * Set timezone based in PHP docs
     * 
     *  @see https://www.php.net/manual/en/timezones.php
     * @param string $timezone
     * @return void
     */
    public static function setTimezone(string $timezone): void
    {
        self::$timezone = $timezone;
    }

    /**
     * Get timezone
     *
     * @return string
     */
    public static function getTimezone(): string
    {
        return self::$timezone;
    }
}
