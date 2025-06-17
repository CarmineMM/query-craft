<?php

namespace CarmineMM\QueryCraft\Casts;

use CarmineMM\QueryCraft\Data\Model;

class FloatCast extends NumericCast
{
    /**
     * Convert the value to a float when getting it.
     *
     * @param mixed $value
     * @param Model $model
     * @return float
     */
    public function get(mixed $value, Model $model): mixed
    {
        if (is_null($value)) {
            return 0.0;
        }

        return (float)$value;
    }

    /**
     * Prepare the value for storage with the specified number of decimals.
     *
     * @param mixed $value
     * @param Model $model
     * @return mixed
     */
    public function set(mixed $value, Model $model): mixed
    {
        if (is_null($value)) {
            return $this->formatNumber(0.0);
        }

        $floatValue = (float)$value;
        
        // If decimals are specified, format the number
        if ($this->decimals > 0) {
            return $this->formatNumber($floatValue);
        }

        return $floatValue;
    }
}
