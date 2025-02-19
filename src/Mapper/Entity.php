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
    public function __loadCasts(): void
    {
        $casts = new Casts;

        if ($createdAtField = $this->model->getCreatedAtField()) {
            $this->$createdAtField = $casts->getter($this->$createdAtField, $this->model, 'datetime');
        }

        if ($updatedAtField = $this->model->getUpdatedAtField()) {
            $this->$updatedAtField = $casts->getter($this->$updatedAtField, $this->model, 'datetime');
        }

        if ($deletedAtField = $this->model->getDeletedAtField()) {
            $this->$deletedAtField = $casts->getter($this->$deletedAtField, $this->model, 'datetime');
        }

        foreach ($this->getCasts() as $key => $cast) {
            $this->$key = $casts->getter($this->$key, $this->model, $cast);
        }
    }
}
