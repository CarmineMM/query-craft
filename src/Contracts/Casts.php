<?php

namespace CarmineMM\QueryCraft\Contracts;

use CarmineMM\QueryCraft\Data\Model;

interface Casts
{
    /**
     * Get cast from the database
     *
     * @param mixed $data
     * @param Model $model
     * @return mixed
     */
    public function get(mixed $data, Model $model): mixed;

    /**
     * Set cast to the database
     *
     * @param mixed $data
     * @param Model $model
     * @return mixed
     */
    public function set(mixed $data, Model $model): mixed;
}
