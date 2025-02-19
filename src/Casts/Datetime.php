<?php

namespace CarmineMM\QueryCraft\Casts;

use CarmineMM\QueryCraft\Contracts\Casts;
use CarmineMM\QueryCraft\Data\Model;
use DateTime as GlobalDateTime;

class Datetime implements Casts
{
    public function get(mixed $data, Model $model): mixed
    {
        return is_a($data, GlobalDateTime::class) ? $data : new GlobalDateTime($data);
    }

    public function set(mixed $data, Model $model): mixed
    {
        return $data;
    }
}
