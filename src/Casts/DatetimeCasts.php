<?php

namespace CarmineMM\QueryCraft\Casts;

use CarmineMM\QueryCraft\Contracts\Casts;
use CarmineMM\QueryCraft\Data\Model;
use DateTime;

class DatetimeCasts implements Casts
{
    /**
     * Get the datetime cast
     *
     * @param mixed $data
     * @param Model $model
     * @return mixed
     */
    public function get(mixed $data, Model $model): mixed
    {
        return is_string($data) ? new DateTime($data) : $data;
    }

    /**
     * Set the datetime cast
     *
     * @param mixed $data
     * @param Model $model
     * @return mixed
     */
    public function set(mixed $data, Model $model): mixed
    {
        return $data instanceof DateTime ? $data->format('Y-m-d H:i:s') : $data;
    }
}
