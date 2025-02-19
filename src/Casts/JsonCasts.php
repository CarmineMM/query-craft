<?php

namespace CarmineMM\QueryCraft\Casts;

use CarmineMM\QueryCraft\Contracts\Casts;
use CarmineMM\QueryCraft\Data\Model;

class JsonCasts implements Casts
{
    public function get(mixed $data, Model $model): mixed
    {
        return json_decode($data);
    }

    public function set(mixed $data, Model $model): mixed
    {
        return json_encode($data);
    }
}
