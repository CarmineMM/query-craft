<?php

namespace CarmineMM\QueryCraft\Mapper;

use CarmineMM\QueryCraft\Adapter\Casts;
use CarmineMM\QueryCraft\Data\Model;
use CarmineMM\QueryCraft\Data\Shapeable;

class Entity
{
    use Castable, Shapeable;

    /**
     * MOdel
     *
     * @var string|Model
     */
    public string|Model $model;

    /**
     * Attributes of the entity
     *
     * @var array
     */
    private array $attributes;

    /**
     * Constructor of the entity
     */
    public function __construct(
        /**
         * Data of the entity
         *
         * @var array
         */
        array $attributes = [],

        /**
         * Model of the entity 
         *
         * @var Model
         */
        ?Model $model = null,

        /**
         * Load include, casts, hidden's fields
         */
        array $loadWith = [
            'casts',
            'hidden',
        ]
    ) {
        if ($model) {
            $this->model = $model;
        } else if (is_string($this->model)) {
            $this->model = new Model();
        }

        $this->setAttributes($attributes, $loadWith);

        $this->attributes = $attributes;
    }

    /**
     * SetAttributes
     *
     * @param array $attributes
     * @return void
     */
    private function setAttributes(array $attributes, array $loadWith): void
    {
        $hiddenFields = in_array('hidden', $loadWith) ? $this->model->getHiddenFields() : [];
        $casts = in_array('casts', $loadWith) ? $this->getCasts() : [];
        $director = new Casts;

        foreach ($attributes as $key => $value) {
            // Hidden fields
            if (in_array($key, $hiddenFields)) {
                continue;
            }

            // Casts Fields and regular fields
            if (in_array($key, array_keys($casts))) {
                $this->$key = $director->getter($value, $this->model, $casts[$key]);
            } else {
                $this->$key = $value;
            }
        }
        $this->attributes = $attributes;

        // Cargar los timestamps casts
        $this->__loadTimestamps($director);
    }

    /**
     * Convert the entity to an array
     *
     * @return array
     */
    public function toArray(): array
    {
        $attributes = [];

        foreach ($this->attributes as $key) {
            $attributes[$key] = $this->$key;
        }

        return $attributes;
    }

    /**
     * Load the casts and apply them to the entity
     *
     * @return void
     */
    private function __loadTimestamps(Casts $casts): void
    {
        $createdAtField = $this->model->getCreatedAtField();
        if ($createdAtField  && isset($this->$createdAtField)) {
            $this->attributes[] = $this->$createdAtField;
            $this->$createdAtField = $casts->getter($this->$createdAtField, $this->model, 'datetime');
        }

        $updatedAtField = $this->model->getUpdatedAtField();
        if ($updatedAtField && isset($this->$updatedAtField)) {
            $this->attributes[] = $this->$updatedAtField;
            $this->$updatedAtField = $casts->getter($this->$updatedAtField, $this->model, 'datetime');
        }

        $deletedAtField = $this->model->getDeletedAtField();
        if ($deletedAtField && isset($this->$deletedAtField)) {
            $this->attributes[] = $this->$deletedAtField;
            $this->$deletedAtField = $casts->getter($this->$deletedAtField, $this->model, 'datetime');
        }
    }
}
