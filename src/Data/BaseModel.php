<?php

namespace CarmineMM\QueryCraft\Data;

use CarmineMM\QueryCraft\Connection;
use CarmineMM\QueryCraft\Contracts\Driver;
use PDO;

abstract class BaseModel extends Timestamps
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
     * @return string
     */
    public function getReturnType(): string
    {
        return match ($this->returnType) {
            'object' => \PDO::FETCH_OBJ,
            'array' => \PDO::FETCH_ASSOC,
            default => $this->returnType,
        };
    }
}
