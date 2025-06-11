<?php

namespace CarmineMM\QueryCraft\Migration;

class Blueprint
{
    /**
     * The table the blueprint describes.
     *
     * @var string
     */
    protected string $table;

    /**
     * The columns that should be added to the table.
     *
     * @var array
     */
    protected array $columns = [];

    /**
     * The character set for the table.
     *
     * @var string|null
     */
    protected ?string $charset = null;

    /**
     * The collation for the table.
     *
     * @var string|null
     */
    protected ?string $collation = null;

    /**
     * Create a new schema blueprint.
     *
     * @param  string  $table
     * @return void
     */
    public function __construct(string $table)
    {
        $this->table = $table;
    }

    /**
     * Add a new column to the blueprint.
     *
     * @param  string  $type
     * @param  string  $name
     * @param  array  $parameters
     * @return \CarmineMM\QueryCraft\Migration\ColumnDefinition
     */
    public function addColumn(string $type, string $name, array $parameters = []): ColumnDefinition
    {
        $attributes = array_merge(compact('type', 'name'), $parameters);
        $this->columns[] = &$attributes;
        return new ColumnDefinition($attributes);
    }

    /**
     * Create a new integer column on the table.
     *
     * @param  string  $column
     * @return \CarmineMM\QueryCraft\Migration\ColumnDefinition
     */
    public function integer(string $column): ColumnDefinition
    {
        return $this->addColumn('integer', $column);
    }

    /**
     * Create a new big integer column on the table.
     *
     * @param  string  $column
     * @return \CarmineMM\QueryCraft\Migration\ColumnDefinition
     */
    public function bigInteger(string $column): ColumnDefinition
    {
        return $this->addColumn('bigInteger', $column);
    }

    /**
     * Create a new auto-incrementing integer (primary key) column on the table.
     *
     * @param  string  $column
     * @return \CarmineMM\QueryCraft\Migration\ColumnDefinition
     */
    public function increments(string $column): ColumnDefinition
    {
        return $this->addColumn('increments', $column);
    }

    /**
     * Create a new string column on the table.
     *
     * @param  string  $column
     * @param  int|null  $length
     * @return \CarmineMM\QueryCraft\Migration\ColumnDefinition
     */
    public function string(string $column, ?int $length = 255): ColumnDefinition
    {
        return $this->addColumn('string', $column, compact('length'));
    }

    /**
     * Specify the character set for the table.
     *
     * @param  string  $charset
     * @return $this
     */
    public function charset(string $charset): static
    {
        $this->charset = $charset;
        return $this;
    }

    /**
     * Specify the collation for the table.
     *
     * @param  string  $collation
     * @return $this
     */
    public function collation(string $collation): static
    {
        $this->collation = $collation;
        return $this;
    }

    /**
     * Get the columns on the blueprint.
     *
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Get the table name.
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Get the character set for the table.
     *
     * @return string|null
     */
    public function getCharset(): ?string
    {
        return $this->charset;
    }

    /**
     * Get the collation for the table.
     *
     * @return string|null
     */
    public function getCollation(): ?string
    {
        return $this->collation;
    }
}
