<?php

namespace CarmineMM\QueryCraft\Contracts;

use CarmineMM\QueryCraft\Data\Model;
use CarmineMM\QueryCraft\Mapper\Entity;

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

    public function whereNotNull(string $column): static;

    public function whereNull(string $column): static;

    public function toSql(string $sentence = 'select'): string;

    public function get(array $columns = ['*']): array;

    public function limit(int $limit, ?int $offset = null): static;

    public function first(array $column = ['*']): mixed;

    public function insert(array $data): array;

    public function delete(): array;

    public function select(array $columns = ['*']): static;

    public function creator(array|Entity $values, Model $model): static;

    public function create(array|Entity $values, Model $model): array;

    public function reset(): static;
}
