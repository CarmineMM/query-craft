<?php

namespace CarmineMM\QueryCraft\Adapter;

use CarmineMM\QueryCraft\Casts\JsonCasts;

class Casts
{
    /**
     * Default casts
     *
     * @var array
     */
    protected array $defaultCastable = [
        'json' => JsonCasts::class,
    ];

    /**
     * Default casts
     *
     * @var array
     */
    protected array $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
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
