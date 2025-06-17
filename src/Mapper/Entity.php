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
     * Extract casts from property attributes
     *
     * @return array
     */
    private function extractCastsFromAttributes(): array
    {
        $casts = [];
        $reflection = new \ReflectionClass($this);
        
        foreach ($reflection->getProperties() as $property) {
            $attributes = $property->getAttributes(\CarmineMM\QueryCraft\Attributes\Cast::class);
            
            if (!empty($attributes)) {
                $cast = $attributes[0]->newInstance();
                $propertyName = $property->getName();
                
                // If there are parameters, store them with the cast type
                if (!empty($cast->parameters)) {
                    $casts[$propertyName] = [
                        'type' => $cast->type,
                        'parameters' => $cast->parameters
                    ];
                } else {
                    $casts[$propertyName] = $cast->type;
                }
            }
        }
        
        return $casts;
    }

    /**
     * SetAttributes
     *
     * @param array $attributes
     * @param array $loadWith
     * @return void
     */
    private function setAttributes(array $attributes, array $loadWith): void
    {
        $hiddenFields = in_array('hidden', $loadWith) ? $this->model->getHiddenFields() : [];
        
        // Get casts from both model and attributes
        $modelCasts = in_array('casts', $loadWith) ? $this->getCasts() : [];
        $attributeCasts = $this->extractCastsFromAttributes();
        
        // Combinar los casts (los atributos tienen prioridad sobre los del modelo)
        $casts = array_merge($modelCasts, $attributeCasts);
        
        $director = new Casts;

        foreach ($attributes as $key => $value) {
            // Hidden fields
            if (in_array($key, $hiddenFields)) {
                continue;
            }

            // Casts Fields and regular fields
            if (isset($casts[$key])) {
                $this->$key = $director->getter($value, $this->model, $casts[$key]);
            } else {
                $this->$key = $value;
            }
        }
        
        $this->attributes = array_keys($attributes);

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
