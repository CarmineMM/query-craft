<?php

namespace CarmineMM\QueryCraft\Contracts;

/**
 * Driver interface
 * 
 * @package CarmineMM\QueryCraft\Contracts
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 * @license MIT
 * @version 1.0.0
 */
interface Driver
{
    public function all(array $columns = ['*']);

    public function where(string $column, string $sentence, string $three = ''): static;

    public function orWhere(string $column, string $sentence, string $three = ''): static;
}
