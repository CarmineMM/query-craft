<?php

namespace CarmineMM\QueryCraft\Mapper;

use CarmineMM\QueryCraft\Adapter\Casts;
use CarmineMM\QueryCraft\Data\Model;

class TempEntity
{
    use Castable;

    /**
     * Attributes
     *
     * @var array
     */
    protected array $attributes = [];

    /**
     * Construct
     *
     * @param Model $model
     */
    public function __construct(
        public Model $model
    ) {
        //        
    }

    /**
     * Set Attributes
     *
     * @param array $attributes
     * @return static
     */
    public function setAttributes(array $attributes): static
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Set Casts
     *
     * @param array $casts
     * @return static
     */
    public function setCasts(array $casts): static
    {
        $this->casts = $casts;

        return $this;
    }

    /**
     * Get Setter casts
     *
     * @return static
     */
    public function getSetterCasts(): static
    {
        $director = new Casts;

        foreach ($this->casts as $key => $value) {
            $this->attributes[$key] = $director->setter($this->attributes[$key], $this->model, $value);
        }

        return $this;
    }

    /**
     * Attributes
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
