<?php

namespace CarmineMM\QueryCraft\Casts;

use CarmineMM\QueryCraft\Data\Model;

class IntCast extends NumericCast
{
    /**
     * Convert the value to an integer when getting it.
     *
     * @param mixed $value
     * @param Model $model
     * @return int
     */
    public function get(mixed $value, Model $model): mixed
    {
        if (is_null($value)) {
            return 0;
        }

        return (int)$value;
    }

    /**
     * Prepare the value for storage.
     *
     * @param mixed $value
     * @param Model $model
     * @return mixed
     */
    public function set(mixed $value, Model $model): mixed
    {
        if (is_null($value)) {
            return 0;
        }

        return (int)$value;
    }
}
