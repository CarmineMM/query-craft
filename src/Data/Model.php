<?php

namespace CarmineMM\QueryCraft\Data;

use CarmineMM\QueryCraft\Adapter\Sanitizer;
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
     * Constructor of the model
     */
    public function __construct(string $connection = 'default')
    {
        if ($this->table === '') {
            $table = explode('\\', get_called_class());
            $this->table = strtolower(
                str_replace('Model', '', array_pop($table))
            ) . 's';
        }

        $this->connection = $connection;

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
     * Set hidden fields
     *
     * @param array $hidden
     * @return static
     */
    public function setHidden(array $hidden): static
    {
        $this->hidden = $hidden;
        return $this;
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
     * Set the table name
     *
     * @param string $table
     * @return static
     */
    public function setTable(string $table): static
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Select all elements of the table
     *
     * @param array|null $columns
     * @return array
     */
    public function all(array|null $columns = null): array
    {
        return Wrapper::wrap(
            $this->driver->all($columns === null ? null : Sanitizer::strings($columns)),
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
     * Where clause for the query
     *
     * @param string $column
     * @return static
     */
    public function whereNotNull(string $column): static
    {
        $this->driver->whereNotNull(Sanitizer::string($column));
        return $this;
    }

    /**
     * Where clause for the query
     *
     * @param string $column
     * @return static
     */
    public function whereNull(string $column): static
    {
        $this->driver->whereNull(Sanitizer::string($column));
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
     * @param array|null $columns
     * @return mixed    
     */
    public function get(array|null $columns = null): mixed
    {
        return Wrapper::wrap(
            $this->driver->get($columns === null ? null : Sanitizer::strings($columns)),
            $this
        );
    }

    /**
     * Select instance
     *
     * @param array $columns
     * @return static
     */
    public function select(array $columns = ['*']): static
    {
        $this->driver->select(Sanitizer::strings($columns));

        return $this;
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

        $this->driver->limit($limit, $offset);

        return $this;
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

    /**
     * Insert Data Massively
     *
     * @return array
     */
    public function insert(array $data): array
    {
        return $this->driver->insert($data);
    }

    /**
     * Countable elements
     *
     * @param string $column
     * @return int
     */
    public function count(string $column = '*'): int
    {
        return $this->driver->count($column);
    }
}
