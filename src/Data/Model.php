<?php

namespace CarmineMM\QueryCraft\Data;

use CarmineMM\QueryCraft\Casts\Castable;

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
    use Timestamps;

    /**
     * Table name
     *
     * @var string
     */
    protected string $table = '';

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
     * Constructor of the model
     */
    public function __construct()
    {
        if ($this->table === '') {
            $table = explode('\\', get_called_class());
            $this->table = strtolower(array_pop($table)) . 's';
        }

        parent::__construct($this);
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
