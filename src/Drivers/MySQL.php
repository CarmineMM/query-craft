<?php

namespace CarmineMM\QueryCraft\Drivers;

use CarmineMM\QueryCraft\Adapter\SQLBaseDriver;
use CarmineMM\QueryCraft\Contracts\Driver;

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
     * Constructor of the MySQL driver
     */
    public function __construct()
    {
        self::$instance = $this;
    }

    public function all(array $columns = ['*'])
    {
        # code...
    }
}
