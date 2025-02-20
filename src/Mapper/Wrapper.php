<?php

namespace CarmineMM\QueryCraft\Mapper;

use CarmineMM\QueryCraft\Data\Model;

final class Wrapper
{
    /**
     * Wrap the data
     *
     * @param array $data
     * @param Model $model
     * @return mixed
     */
    public static function wrap(array $data, Model $model): mixed
    {
        return $data;
    }
}
