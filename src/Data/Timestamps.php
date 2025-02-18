<?php

namespace CarmineMM\QueryCraft\Data;

abstract class Timestamps
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
     * @var boolean
     */
    protected string $created_field = 'created_at';

    /**
     * Updated at field
     *
     * @var string
     */
    protected string $updated_field = 'updated_at';

    /**
     * Deleted at field
     *
     * @var string
     */
    protected string $deleted_field = 'deleted_at';
}
