<?php

namespace CarmineMM\QueryCraft\Mapper;

use CarmineMM\QueryCraft\Data\Model;

/**
 * Modelar la data, antes de ejecutar transacciones sobre la base de datos.
 * 
 * @package CarmineMM\QueryCraft
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 * @license MIT
 * @version 1.0.0
 */
class Modeling
{
    public static function fillableData(Model $model, array|Entity $values): array
    {
        $values = $values instanceof Entity ? $values->toArray() : $values;

        return $values;
    }
}
