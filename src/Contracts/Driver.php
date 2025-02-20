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

    public function toSql(string $sentence = 'select'): string;

    public function get(array $columns = ['*']): array;

    public function limit(int $limit, ?int $offset = null): static;

    public function first(array $column = ['*']): mixed;
}
