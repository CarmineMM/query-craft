<?php

namespace CarmineMM\QueryCraft\Data;

use CarmineMM\QueryCraft\Adapter\Sanitizer;
use CarmineMM\QueryCraft\DB;
use CarmineMM\QueryCraft\Mapper\Entity;
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
     * Hidden fields
     *
     * @var array
     */
    protected array $hidden = [];

    /**
     * Allow to eliminate all elements
     *
     * @var boolean
     */
    public bool $allow_bulk_delete = false;

    /**
     * Constructor of the model
     */
    public function __construct()
    {
        if ($this->table === '') {
            $table = explode('\\', get_called_class());
            $this->table = strtolower(array_pop($table)) . 's';
        }

        $this->allow_bulk_delete = DB::isMassDeletionAllowed();

        parent::__construct();
    }

    /**
     * Get the hidden fields
     *
     * @return array
     */
    public function getHiddenFields(): array
    {
        return $this->hidden;
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
            $this->driver->all(Sanitizer::strings($columns)),
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
        $column = Sanitizer::string($column);
        $sentence = Sanitizer::string($sentence);
        $three = Sanitizer::string($three);

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
        $column = Sanitizer::string($column);
        $sentence = Sanitizer::string($sentence);
        $three = Sanitizer::string($three);

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
        return Wrapper::wrap(
            $this->driver->get(Sanitizer::strings($columns)),
            $this
        );
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
        $limit = Sanitizer::integer($limit);

        if ($offset) {
            $offset = Sanitizer::integer($offset);
        }

        return $this->driver->limit($limit, $offset);
    }

    /**
     * First element of the table
     *
     * @param array $columns
     * @return mixed
     */
    public function first(array $columns = ['*']): mixed
    {
        return $this->driver->first(Sanitizer::strings($columns));
    }

    /**
     * Reset the query
     *
     * @return static
     */
    public function reset(): static
    {
        $this->driver->reset();

        return $this;
    }

    /**
     * Create one element
     *
     * @param array|Entity $values
     * @return array
     */
    public function create(array|Entity $values): array
    {
        return $this->driver->create($values, $this);
    }

    /**
     * Create one element
     *
     * @param array|Entity $values
     * @return static
     */
    public function creator(array|Entity $values): static
    {
        $this->driver->creator($values, $this);

        return $this;
    }
}
