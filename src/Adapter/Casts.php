<?php

namespace CarmineMM\QueryCraft\Adapter;

use CarmineMM\QueryCraft\Casts\Datetime;
use CarmineMM\QueryCraft\Casts\JsonCasts;
use CarmineMM\QueryCraft\Data\Model;

class Casts
{
    /**
     * Default casts
     *
     * @var array
     */
    protected array $defaultCastable = [
        'json' => JsonCasts::class,
        'datetime' => Datetime::class,
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
    private function applyCast(mixed $data, string $cast, Model $model, string $option): mixed
    {
        // Comprobar si la clase existe
        if (!class_exists($cast)) {
            throw new \Exception("The cast {$cast} does not exist", 500);
        }

        return (new $cast)->{$option}($data, $model);
    }

    /**
     * Casts de tipo getter
     *
     * @param mixed $data
     * @param string $cast
     * @return mixed
     */
    public function getter(mixed $data, Model $model, string $cast): mixed
    {
        // Comprobar si el cast predeterminado existe
        if (isset($this->defaultCastable[$cast])) {
            return $this->applyCast($data, $this->defaultCastable[$cast], $model, 'get');
        }

        return $this->applyCast($data, $cast, $model, 'get');
    }
}
