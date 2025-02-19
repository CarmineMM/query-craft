<?php

namespace CarmineMM\QueryCraft\Mapper;

trait Castable
{
    /**
     * Use casts 
     * 
     * @var array
     */
    protected array $casts = [];

    /**
     * Enables the use of casts, the created_at and updated_at will be transformed,
     * and Soft Deletes on dates. In addition to custom Casts.<array> $ casts
     *
     * @var boolean
     */
    protected bool $useCasts = true;

    /**
     * Solve if you have casts
     *
     * @return boolean
     */
    public function hasCasts(): bool
    {
        return $this->useCasts && count($this->casts) > 0;
    }

    /**
     * Get the casts
     *
     * @return array
     */
    public function getCasts(): array
    {
        return $this->useCasts ? $this->casts : [];
    }
}
