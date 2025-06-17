<?php

namespace CarmineMM\QueryCraft\Casts;

use CarmineMM\QueryCraft\Contracts\Casts;
use CarmineMM\QueryCraft\Data\Model;
use CarmineMM\QueryCraft\Facades\DB;
use DateTime;
use DateTimeZone;

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
        return is_string($data)
            ? (new DateTime($data))->setTimezone(
                new DateTimeZone(DB::getTimezone())
            )
            : $data;
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
        return $data instanceof DateTime
            ? $data->setTimezone(new DateTimeZone(DB::getTimezone()))->format('Y-m-d H:i:s')
            : $data;
    }
}
