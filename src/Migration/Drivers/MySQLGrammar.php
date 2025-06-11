<?php

namespace CarmineMM\QueryCraft\Migration\Drivers;

use CarmineMM\QueryCraft\Migration\Blueprint;
use CarmineMM\QueryCraft\Migration\Contracts\Grammar;

class MySQLGrammar implements Grammar
{
    /**
     * Compile a create table command.
     *
     * @param  \CarmineMM\QueryCraft\Migration\Blueprint  $blueprint
     * @return string
     */
    public function compileCreate(Blueprint $blueprint): string
    {
        $table = '`' . $blueprint->getTable() . '`';
        $columns = implode(', ', $this->getColumns($blueprint));

        $sql = "CREATE TABLE {$table} ({$columns})";

        // Add charset and collation if specified
        if ($charset = $blueprint->getCharset()) {
            $sql .= ' DEFAULT CHARACTER SET ' . $charset;
        }

        if ($collation = $blueprint->getCollation()) {
            $sql .= ' COLLATE ' . $collation;
        }

        return $sql;
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
            $sql = '`' . $column['name'] . '` ' . $this->getType($column);

            // The 'increments' type is a special case and handles its own modifiers.
            if ($column['type'] === 'increments') {
                $sql .= ' UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY';
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
    protected function getType(array $column): string
    {
        return match ($column['type']) {
            'increments' => 'INT',
            'integer' => 'INT',
            'bigInteger' => 'BIGINT',
            'string' => 'VARCHAR(' . ($column['length'] ?? 255) . ')',
            default => $column['type'],
        };
    }

    /**
     * Add the modifiers to the SQL.
     *
     * @param  string  $sql
     * @param  array  $column
     * @return string
     */
    protected function addModifiers(string $sql, array $column): string
    {
        if (isset($column['unsigned']) && $column['unsigned']) {
            $sql .= ' UNSIGNED';
        }

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
}
