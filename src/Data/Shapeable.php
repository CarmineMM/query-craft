<?php

namespace CarmineMM\QueryCraft\Data;

/**
 * Puede heredar funcione y acciones de los modelos,
 * basándome en las configuraciones del mismo.
 */
trait Shapeable
{
    /**
     * Delete current element
     *
     * @return array
     */
    public function delete(): array
    {
        $primaryKey = $this->model->getPrimaryKey();

        return $this->model
            ->where($primaryKey, $this->{$primaryKey})
            ->delete();
    }
}
