<?php

namespace CarmineMM\QueryCraft\Data;

use CarmineMM\QueryCraft\Connection;
use CarmineMM\QueryCraft\Contracts\Driver;
use CarmineMM\QueryCraft\Mapper\Entity;
use PDO;

abstract class BaseModel
{
    /**
     * Connection name
     *
     * @var string
     */
    protected string $connection = 'default';

    /**
     * Return type, the returnable types are: array, object, EntityClass.
     *
     * @var string
     */
    protected string $returnType = 'array';

    /**
     * Using cache
     *
     * @var boolean
     */
    protected bool $cache = true;

    /**
     * PDO Connection
     *
     * @method array get(array $columns = ['*'])
     * @var Driver
     */
    protected Driver $driver;

    /**
     * Wrapping the list of elements
     *
     * @var string
     */
    protected string $wrap = 'array';

    /**
     * Primary key
     *
     * @var string
     */
    protected string $primaryKey = 'id';

    /**
     * Fillable fields
     *
     * @var array
     */
    protected array $fillable = [];

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public bool $hasAutoIncrement = true;

    /**
     * Constructor of the base model
     */
    public function __construct()
    {
        $this->driver = Connection::pdo($this->connection, $this);
        $this->cache = Connection::$instance->cache;
    }

    /**
     * Get primary key
     *
     * @return string|integer
     */
    public function getPrimaryKey(): string|int
    {
        return $this->primaryKey;
    }

    /**
     * Use cache in queries or this query
     *
     * @param boolean $useCache
     * @return static
     */
    public function useCache(bool $useCache = true): static
    {
        $this->cache = $useCache;

        return $this;
    }

    /**
     * Check if the model has cache
     *
     * @return boolean
     */
    public function hasCache(): bool
    {
        return $this->cache;
    }

    /**
     * Set return type
     *
     * @param string $returnType
     * @return static
     */
    public function setReturnType(string $returnType): static
    {
        $this->returnType = $returnType;

        return $this;
    }

    /**
     * Wrap the result of the query
     *
     * @return string|int|Entity
     */
    public function getReturnType(): string|int
    {
        return match ($this->returnType) {
            'object' => \PDO::FETCH_OBJ,
            'array' => \PDO::FETCH_ASSOC,
            default => class_exists($this->returnType)
                ? $this->returnType
                // If you see an error here, it is because the type of return does not exist.
                // The return must be a type of valid data of PHP or a class,
                // Make sure the return is a class or failing an entity.
                : throw new \Exception("The return type {$this->returnType} does not exist", 500),
        };
    }

    /**
     * Get the connection name
     *
     * @return string
     */
    public function getConnection(): string
    {
        return $this->connection;
    }

    /**
     * Set connection name
     *
     * @param string $connection
     * @return static
     */
    public function setConnection(string $connection): static
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * Get fillable fields
     *
     * @return array
     */
    public function getFillable(): array
    {
        return $this->fillable;
    }

    /**
     * Delete element(s)
     *
     * @return array
     */
    public function delete(): array
    {
        return $this->driver->delete();
    }
}
