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
     * Hidden fields
     *
     * @return void
     */
    private function __hiddenFields(): void
    {
        foreach ($this->model->getHiddenFields() as $key => $value) {
            unset($this->$key);
        }
    }

    /**
     * Load the casts and apply them to the entity
     *
     * @return void
     */
    private function __loadCasts(): void
    {
        $casts = new Casts;

        $createdAtField = $this->model->getCreatedAtField();
        if ($createdAtField  && isset($this->$createdAtField)) {
            $this->$createdAtField = $casts->getter($this->$createdAtField, $this->model, 'datetime');
        }

        $updatedAtField = $this->model->getUpdatedAtField();
        if ($updatedAtField && isset($this->$updatedAtField)) {
            $this->$updatedAtField = $casts->getter($this->$updatedAtField, $this->model, 'datetime');
        }

        $deletedAtField = $this->model->getDeletedAtField();
        if ($deletedAtField && isset($this->$deletedAtField)) {
            $this->$deletedAtField = $casts->getter($this->$deletedAtField, $this->model, 'datetime');
        }

        foreach ($this->getCasts() as $key => $cast) {
            if (isset($this->$key)) {
                $this->$key = $casts->getter($this->$key, $this->model, $cast);
            } else {
                unset($this->$key);
            }
        }
    }
}
