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
        // Instancia de la entidad basado en el tipo de retorno.
        $returnType = $model->getReturnType();

        if (is_int($returnType)) {
            return $data;
        }

        $self = new self;

        // foreach ($data as $key => $item) {
        //     foreach ($item as $property => $value) {
        //         $data[$key]->$property = $value;
        //     }
        // }

        return $data;
    }
}
