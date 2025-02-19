<?php

namespace CarmineMM\QueryCraft\Mapper;

use CarmineMM\QueryCraft\Adapter\Casts;
use CarmineMM\QueryCraft\Data\Model;
use DateTime;

abstract class Entity
{
    use Castable;

    /**
     * Constructor of the entity
     */
    public function __construct(
        /**
         * Model of the entity 
         *
         * @var Model
         */
        public Model $model
    ) {
        $this->__loadCasts();
    }

    /**
     * Load the casts and apply them to the entity
     *
     * @return void
     */
    public function __loadCasts()
    {
        $casts = new Casts;

        if ($createdAtField = $this->model->getCreatedAtField()) {
            $this->$createdAtField = $casts->getter($this->$createdAtField, $this->model, 'datetime');
        }

        if ($updatedAtField = $this->model->getUpdatedAtField()) {
            $this->$updatedAtField = $casts->getter($this->$updatedAtField, $this->model, 'datetime');
        }



        // Timestamps (CreatedAt, UpdatedAt, DeletedAt)
    }
}
