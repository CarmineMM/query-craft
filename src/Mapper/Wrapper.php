<?php

namespace CarmineMM\QueryCraft\Mapper;

use CarmineMM\QueryCraft\Adapter\Casts;
use CarmineMM\QueryCraft\Data\Model;

final class Wrapper
{
    use Casts;

    /**
     * Wrap the data
     *
     * @param array $data
     * @param Model $model
     * @return mixed
     */
    public function wrap(array $data, Model $model): mixed
    {
        if (!$model->hasCasts()) {
            return $data;
        }

        // Apply the casts
        return $data;
    }
}
