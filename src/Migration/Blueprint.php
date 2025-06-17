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
     * Create a new auto-incrementing unsigned big integer (primary key) column on the table.
     *
     * @param  string  $column
     * @return \CarmineMM\QueryCraft\Migration\ColumnDefinition
     */
    /**
     * Create a new auto-incrementing unsigned big integer (primary key) column on the table.
     *
     * @param  string  $column
     * @return \CarmineMM\QueryCraft\Migration\ColumnDefinition
     */
    public function increments(string $column, string $length = 'big'): ColumnDefinition
    {
        return $this->addColumn('increments', $column, ['length' => $length]);
    }

    /**
     * Create a new auto-incrementing unsigned big integer (primary key) column named 'id'.
     *
     * @return \CarmineMM\QueryCraft\Migration\ColumnDefinition
     */
    public function id(): ColumnDefinition
    {
        return $this->increments('id');
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
     * Create a new text column on the table.
     *
     * @param  string  $column
     * @return \CarmineMM\QueryCraft\Migration\ColumnDefinition
     */
    public function text(string $column): ColumnDefinition
    {
        return $this->addColumn('text', $column);
    }

    /**
     * Create a new medium text column on the table.
     *
     * @param  string  $column
     * @return \CarmineMM\QueryCraft\Migration\ColumnDefinition
     */
    public function mediumText(string $column): ColumnDefinition
    {
        return $this->addColumn('mediumText', $column);
    }

    /**
     * Create a new long text column on the table.
     *
     * @param  string  $column
     * @return \CarmineMM\QueryCraft\Migration\ColumnDefinition
     */
    public function longText(string $column): ColumnDefinition
    {
        return $this->addColumn('longText', $column);
    }

    /**
     * Create a new tiny integer column on the table.
     *
     * @param  string  $column
     * @return \CarmineMM\QueryCraft\Migration\ColumnDefinition
     */
    public function tinyInteger(string $column): ColumnDefinition
    {
        return $this->addColumn('tinyInteger', $column);
    }

    /**
     * Create a new small integer column on the table.
     *
     * @param  string  $column
     * @return \CarmineMM\QueryCraft\Migration\ColumnDefinition
     */
    public function smallInteger(string $column): ColumnDefinition
    {
        return $this->addColumn('smallInteger', $column);
    }

    /**
     * Create a new medium integer column on the table.
     *
     * @param  string  $column
     * @return \CarmineMM\QueryCraft\Migration\ColumnDefinition
     */
    public function mediumInteger(string $column): ColumnDefinition
    {
        return $this->addColumn('mediumInteger', $column);
    }

    /**
     * Create a new enum column on the table.
     *
     * @param  string  $column
     * @param  array  $allowed
     * @return \CarmineMM\QueryCraft\Migration\ColumnDefinition
     */
    public function enum(string $column, array $allowed): ColumnDefinition
    {
        return $this->addColumn('enum', $column, compact('allowed'));
    }

    /**
     * Create a new timestamp column on the table.
     *
     * @param  string  $column
     * @return \CarmineMM\QueryCraft\Migration\ColumnDefinition
     */
    public function timestamp(string $column): ColumnDefinition
    {
        return $this->addColumn('timestamp', $column);
    }

    /**
     * Add nullable creation and update timestamps to the table.
     *
     * @return void
     */
    public function timestamps(): void
    {
        $this->timestamp('created_at')->nullable();
        $this->timestamp('updated_at')->nullable();
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
