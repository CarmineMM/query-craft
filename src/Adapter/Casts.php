<?php

namespace CarmineMM\QueryCraft\Adapter;

use CarmineMM\QueryCraft\Casts\JsonCasts;

trait Casts
{
    /**
     * Default casts
     *
     * @var array
     */
    protected array $defaultCasts = [
        'json' => JsonCasts::class,
    ];

    /**
     * Aplicar los casts
     *
     * @return mixed
     */
    protected function applyCast(mixed $data, mixed $casts): mixed
    {
        return $data;
    }
}
