<?php

namespace CarmineMM\QueryCraft\Data;

trait Timestamps
{
    /**
     * Enable all timestamps
     *
     * @var boolean
     */
    protected bool $timestamps = true;

    /**
     * Use soft deletes
     *
     * @var boolean
     */
    protected bool $softDeletes = false;

    /**
     * Fields timestamps
     *
     * @var null|string
     */
    protected null|string $created_field = 'created_at';

    /**
     * Updated at field
     *
     * @var null|string
     */
    protected null|string $updated_field = 'updated_at';

    /**
     * Deleted at field
     *
     * @var null|string
     */
    protected null|string $deleted_field = 'deleted_at';

    /**
     * Indica si se hace uso de los timestamps
     *
     * @return boolean
     */
    public function hasTimestamps(): bool
    {
        return $this->timestamps;
    }

    /**
     * Get the created at field
     *
     * @return null|string
     */
    public function getCreatedAtField(): null|string
    {
        return $this->timestamps ? $this->created_field : null;
    }

    /**
     * Get the updated at field
     *
     * @return null|string
     */
    public function getUpdatedAtField(): null|string
    {
        return $this->timestamps ? $this->updated_field : null;
    }

    /**
     * Get the deleted at field
     *
     * @return null|string
     */
    public function getDeletedAtField(): null|string
    {
        return $this->softDeletes ? $this->deleted_field : null;
    }
}
