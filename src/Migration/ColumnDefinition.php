<?php

namespace CarmineMM\QueryCraft\Migration;

class ColumnDefinition
{
    /**
     * The attributes for the column.
     *
     * @var array
     */
    public array $attributes = [];

    /**
     * Create a new column definition.
     *
     * @param  array  &$attributes
     * @return void
     */
    public function __construct(array &$attributes)
    {
        $this->attributes = &$attributes;
    }

    /**
     * Specify that the column should be unsigned.
     *
     * @return $this
     */
    public function unsigned(): static
    {
        $this->attributes['unsigned'] = true;
        return $this;
    }

    /**
     * Allow NULL values to be inserted into the column.
     *
     * @return $this
     */
    public function nullable(): static
    {
        $this->attributes['nullable'] = true;
        return $this;
    }

    /**
     * Add a unique constraint to the column.
     *
     * @return $this
     */
    public function unique(): static
    {
        $this->attributes['unique'] = true;
        return $this;
    }
}
