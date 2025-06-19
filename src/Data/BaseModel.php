<?php

namespace CarmineMM\QueryCraft\Data;

use CarmineMM\QueryCraft\Facades\Connection;
use CarmineMM\QueryCraft\Contracts\Driver;
use CarmineMM\QueryCraft\Facades\DB;
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
     * @var string|Entity
     */
    protected string|Entity $returnType = 'array';

    /**
     * Using cache
     *
     * @var boolean
     */
    protected bool $cache = true;

    /**
     * PDO Connection
     *
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
     * Allow to eliminate all elements
     *
     * @var boolean
     */
    public bool $allow_bulk_delete = false;

    /**
     * Allow to update all elements
     *
     * @var boolean
     */
    public bool $allow_bulk_update = false;

    /**
     * Schema de la base de datos
     *
     * @var string
     */
    private string $schema = '';

    /**
     * Constructor of the base model
     */
    /**
     * The raw SQL query to be executed
     *
     * @var string|null
     */
    protected ?string $rawQuery = null;

    public function __construct()
    {
        $this->driver = Connection::pdo($this->connection, $this);
        $this->cache = Connection::$instance->cache;
        $this->allow_bulk_delete = DB::isMassDeletionAllowed();
    }

    /**
     * Set a raw SQL query to be executed
     * 
     * This method allows you to set a raw SQL query that will be executed
     * when the exec() method is called. The query is not executed immediately,
     * allowing you to chain other methods or set bindings before execution.
     * 
     * @param string $sql The raw SQL query to execute
     * @return static Returns the current model instance for method chaining
     */
    public function query(string $sql): static
    {
        $this->rawQuery = $sql;
        return $this;
    }

    /**
     * Execute the raw SQL query set by the query() method
     * 
     * This method executes the raw SQL query that was previously set using the query() method.
     * The query is executed using the driver's statement() method with optional parameter binding.
     * 
     * @param array $bindings Associative array of parameter bindings (e.g., ['id' => 1, 'status' => 'active'])
     * @return bool Returns true on success or false on failure
     * @throws \RuntimeException If no query has been set using query()
     */
    public function exec(array $bindings = []): bool
    {
        if ($this->rawQuery === null) {
            throw new \RuntimeException('No query has been set. Use the query() method to set a raw SQL query.');
        }

        // If no bindings provided, execute the query directly
        if (empty($bindings)) {
            $result = $this->driver->statement($this->rawQuery);
        } else {
            // Prepare and execute with bindings
            $stmt = $this->driver->getPdo()->prepare($this->rawQuery);
            $result = $stmt->execute($bindings);
        }

        // Clear the query after execution
        $this->rawQuery = null;

        return $result !== false;
    }

    /**
     * Set schema
     *
     * @param string $schema
     * @return static
     */
    public function setSchema(string $schema): static
    {
        $this->schema = $schema;
        return $this;
    }

    /**
     * Get schema
     *
     * @return string
     */
    public function getSchema(): string
    {
        return $this->schema;
    }

    /**
     * Access the driver that uses the model
     *
     * @return Driver
     */
    public function getDriver(): Driver
    {
        return $this->driver;
    }

    /**
     * Allow to eliminate all elements
     *
     * @return static
     */
    public function allowBulkDelete(): static
    {
        $this->allow_bulk_delete = true;
        return $this;
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
     * Set primary key
     *
     * @param string $primaryKey
     * @return static
     */
    public function setPrimaryKey(string $primaryKey): static
    {
        $this->primaryKey = $primaryKey;
        return $this;
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
     * @param string|Entity $returnType
     * @return static
     */
    public function setReturnType(string|Entity $returnType): static
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

    /**
     * Set fillable fields
     *
     * @return static
     */
    public function setFillable(array $fillable): static
    {
        $this->fillable = $fillable;
        return $this;
    }
}
