<?php

namespace CarmineMM\QueryCraft\Data;

use CarmineMM\QueryCraft\Casts\Castable;
use CarmineMM\QueryCraft\Connection;
use CarmineMM\QueryCraft\Contracts\Driver;
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
     * PDO Connection
     *
     * @var PDO
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
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public bool $hasAutoIncrement = true;

    /**
     * Constructor of the base model
     */
    public function __construct(Model $model)
    {
        $this->driver = Connection::pdo($this->connection, $model);
    }

    /**
     * Wrap the result of the query
     *
     * @return string|int
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
}
