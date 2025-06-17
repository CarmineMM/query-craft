<?php

namespace CarmineMM\QueryCraft\Adapter;

use CarmineMM\QueryCraft\Casts as TheCasts;
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
        'json' => TheCasts\JsonCasts::class,
        'object' => TheCasts\JsonCasts::class,
        'datetime' => TheCasts\DatetimeCasts::class,
        'array' => TheCasts\ArrayCasts::class,
        'int' => TheCasts\IntCast::class,
        'integer' => TheCasts\IntCast::class,
        'float' => TheCasts\FloatCast::class,
        'double' => TheCasts\FloatCast::class,
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
     * Getter type casts
     *
     * @param mixed $data
     * @param Model $model
     * @param string|array $cast
     * @return mixed
     */
    public function getter(mixed $data, Model $model, $cast): mixed
    {
        $castType = is_array($cast) ? $cast['type'] : $cast;
        $parameters = is_array($cast) ? ($cast['parameters'] ?? []) : [];
        
        // Check if the default cast exists
        if (isset($this->defaultCastable[$castType])) {
            $castClass = $this->defaultCastable[$castType];
            $instance = !empty($parameters) ? new $castClass(...$parameters) : new $castClass();
            return $instance->get($data, $model);
        }

        // For custom cast classes
        if (!class_exists($castType)) {
            throw new \Exception("The cast {$castType} does not exist", 500);
        }
        
        $instance = !empty($parameters) ? new $castType(...$parameters) : new $castType();
        return $instance->get($data, $model);
    }

    /**
     * Set casts
     *
     * @param mixed $data
     * @param Model $model
     * @param string|array $cast
     * @return mixed
     */
    public function setter(mixed $data, Model $model, $cast): mixed
    {
        $castType = is_array($cast) ? $cast['type'] : $cast;
        $parameters = is_array($cast) ? ($cast['parameters'] ?? []) : [];
        
        // Check if the default cast exists
        if (isset($this->defaultCastable[$castType])) {
            $castClass = $this->defaultCastable[$castType];
            $instance = !empty($parameters) ? new $castClass(...$parameters) : new $castClass();
            return $instance->set($data, $model);
        }

        // For custom cast classes
        if (!class_exists($castType)) {
            throw new \Exception("The cast {$castType} does not exist", 500);
        }
        
        $instance = !empty($parameters) ? new $castType(...$parameters) : new $castType();
        return $instance->set($data, $model);
    }
}
