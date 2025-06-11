<?php

namespace CarmineMM\QueryCraft\Migration;

use Closure;
use CarmineMM\QueryCraft\Connection;
use CarmineMM\QueryCraft\Drivers\MySQL;
use CarmineMM\QueryCraft\Drivers\PostgresSQL;
use CarmineMM\QueryCraft\Drivers\SQLServer;
use CarmineMM\QueryCraft\Migration\Drivers\MySQLGrammar;
use CarmineMM\QueryCraft\Migration\Drivers\PostgresSQLGrammar;
use CarmineMM\QueryCraft\Migration\Drivers\SQLServerGrammar;
use Exception;

class Schema
{
    /**
     * Create a new table on the schema.
     *
     * @param  string  $table
     * @param  \Closure  $callback
     * @return void
     */
    public static function create(string $table, Closure $callback): void
    {
        $blueprint = new Blueprint($table);

        $callback($blueprint);

        static::build($blueprint);
    }

    /**
     * Build the blueprint.
     *
     * @param  \CarmineMM\QueryCraft\Migration\Blueprint  $blueprint
     * @return void
     */
    protected static function build(Blueprint $blueprint): void
    {
        $connection = Connection::pdo();

        $grammar = match (get_class($connection)) {
            PostgresSQL::class => new PostgresSQLGrammar(),
            SQLServer::class => new SQLServerGrammar(),
            MySQL::class => new MySQLGrammar(),
            default => throw new Exception('Unsupported driver for migrations.'),
        };

        $queries = [$grammar->compileCreate($blueprint)];

        foreach ($queries as $query) {
            $connection->statement($query);
        }
    }
}
