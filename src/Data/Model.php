<?php

namespace CarmineMM\QueryCraft\Data;

use CarmineMM\QueryCraft\Casts\Castable;
use CarmineMM\QueryCraft\Mapper\Wrapper;

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

        parent::__construct();
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
        return Wrapper::wrap(
            $this->driver->all($columns),
            $this
        );
    }

    /**
     * Where clause for the query
     *
     * @param string $column
     * @param string $sentence
     * @param string $three
     * @return static
     */
    public function where(string $column, string $sentence, string $three = ''): static
    {
        $this->driver->where($column, $sentence, $three);
        return $this;
    }

    /**
     * Or where clause for the query
     *
     * @param string $column
     * @param string $sentence
     * @param string $three
     * @return static
     */
    public function orWhere(string $column, string $sentence, string $three = ''): static
    {
        $this->driver->orWhere($column, $sentence, $three);
        return $this;
    }

    /**
     * View the SQL query
     *
     * @return string
     */
    public function toSql(): string
    {
        return $this->driver->toSql();
    }

    /**
     * Get elements of the table
     *
     * @param array $columns
     * @return mixed
     */
    public function get(array $columns = ['*']): mixed
    {
        return Wrapper::wrap($this->driver->get($columns), $this);
    }

    /**
     * Limit and offset for the query
     *
     * @param integer $limit
     * @param integer|null $offset
     * @return static
     */
    public function limit(int $limit, ?int $offset = null): static
    {
        return $this->driver->limit($limit, $offset);
    }

    /**
     * First element of the table
     *
     * @param array $column
     * @return mixed
     */
    public function first(array $column = ['*']): mixed
    {
        return $this->driver->first($column);
    }
}
