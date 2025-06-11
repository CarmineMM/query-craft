<?php

namespace CarmineMM\QueryCraft\Migration\Drivers;

use CarmineMM\QueryCraft\Migration\Blueprint;
use CarmineMM\QueryCraft\Migration\Contracts\Grammar;

class SQLServerGrammar implements Grammar
{
    /**
     * Compile a create table command.
     *
     * @param  \CarmineMM\QueryCraft\Migration\Blueprint  $blueprint
     * @return string
     */
    public function compileCreate(Blueprint $blueprint): string
    {
        $table = $blueprint->getTable();
        $columns = implode(', ', $this->getColumns($blueprint));

        return "CREATE TABLE [{$table}] ({$columns})";
    }

    /**
     * Get the SQL for the columns.
     *
     * @param  \CarmineMM\QueryCraft\Migration\Blueprint  $blueprint
     * @return array
     */
    protected function getColumns(Blueprint $blueprint): array
    {
        $columns = [];

        foreach ($blueprint->getColumns() as $column) {
            $sql = '[' . $column['name'] . '] ' . $this->getType($column);

            // The 'increments' type is a special case and handles its own modifiers.
            if ($column['type'] === 'increments') {
                $sql .= ' IDENTITY(1,1) PRIMARY KEY'; // PRIMARY KEY implies NOT NULL
            } else {
                $sql = $this->addModifiers($sql, $column);
            }

            $columns[] = $sql;
        }

        return $columns;
    }

    /**
     * Get the SQL for the column type.
     *
     * @param  array  $column
     * @return string
     */
    /**
     * Add the modifiers to the SQL.
     *
     * @param  string  $sql
     * @param  array  $column
     * @return string
     */
    protected function addModifiers(string $sql, array $column): string
    {
        if (isset($column['nullable']) && $column['nullable']) {
            $sql .= ' NULL';
        } else {
            $sql .= ' NOT NULL';
        }

        if (isset($column['unique']) && $column['unique']) {
            $sql .= ' UNIQUE';
        }

        return $sql;
    }

    /**
     * Get the SQL for the column type.
     *
     * @param  array  $column
     * @return string
     */
    protected function getType(array $column): string
    {
        return match ($column['type']) {
            'increments' => 'INT',
            'integer' => 'INT',
            'tinyInteger' => 'TINYINT',
            'smallInteger' => 'SMALLINT',
            'mediumInteger' => 'INT', // SQL Server does not have a MEDIUMINT type
            'bigInteger' => 'BIGINT',
            'string' => 'NVARCHAR(' . ($column['length'] ?? 255) . ')',
            'text' => 'NVARCHAR(MAX)',
            'mediumText' => 'NVARCHAR(MAX)',
            'longText' => 'NVARCHAR(MAX)',
            'enum' => 'NVARCHAR(' . ($column['length'] ?? 255) . ')', // SQL Server ENUM type is handled with a CHECK constraint, for simplicity we use NVARCHAR
            'timestamp' => 'DATETIME2',
            default => $column['type'],
        };
    }
}
