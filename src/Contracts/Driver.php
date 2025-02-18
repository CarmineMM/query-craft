<?php

namespace CarmineMM\QueryCraft\Contracts;

/**
 * Driver interface
 * 
 * @method mixed all()
 * 
 * @package CarmineMM\QueryCraft\Contracts
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 * @license MIT
 * @version 1.0.0
 */
interface Driver
{
    public function all(array $columns = ['*']);
}
