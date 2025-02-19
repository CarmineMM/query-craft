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
    public static function wrap(array $data, Model $model): mixed
    {
        // Instancia de la entidad basado en el tipo de retorno.
        $model->getReturnType();

        // Apply the casts
        return $data;
    }
}
