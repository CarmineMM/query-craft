<?php

namespace CarmineMM\QueryCraft\Data;

/**
 * Model class
 * 
 * @package CarmineMM\QueryCraft
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 * @license MIT
 * @version 1.0.0
 */
class Model extends BaseModel
{
    /**
     * Table name
     *
     * @var string
     */
    protected string $table;

    /**
     * Fillable fields
     *
     * @var array
     */
    protected array $fillable = [];

    /**
     * Hidden fields
     *
     * @var array
     */
    protected array $hidden = [];

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
     * Constructor of the model
     */
    public function __construct()
    {
        $this->table = $this->table ?? (strtolower(str_replace('\\', '', get_class($this))) . 's');

        parent::__construct($this);
    }

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
     * Get the table name
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Select all elements of the table
     *
     * @param array $columns
     * @return array
     */
    public function all(array $columns = ['*']): array
    {
        return $this->driver->all($columns);
    }
}
