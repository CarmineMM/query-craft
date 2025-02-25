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
     * Sanitized encoding
     *
     * @var string
     */
    protected static string $sanitize_encoding = 'UTF-8';

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

    /**
     * Set sanitize encoding
     *
     * @param string $encoding
     * @return void
     */
    public  static function setSanitizeEncoding(string $encoding = 'UTF-8'): void
    {
        self::$sanitize_encoding = $encoding;
    }

    /**
     * Get sanitize encoding
     *
     * @return string
     */
    public static function getSanitizeEncoding(): string
    {
        return self::$sanitize_encoding;
    }
}
