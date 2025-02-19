<?php

namespace CarmineMM\QueryCraft\Adapter;

use CarmineMM\QueryCraft\Casts\DatetimeCasts;
use CarmineMM\QueryCraft\Casts\JsonCasts;
use CarmineMM\QueryCraft\Contracts\Casts as ContractsCasts;
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
        'object' => JsonCasts::class,
        'datetime' => DatetimeCasts::class,
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

        if (!in_array(ContractsCasts::class, class_implements($cast))) {
            // must implement the casts contract
            // If it does not implement it, an exception is launched
            // Verify if your casts implements the casts contract.
            throw new \Exception("The cast {$cast} must implement the Casts Contract", 500);
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
