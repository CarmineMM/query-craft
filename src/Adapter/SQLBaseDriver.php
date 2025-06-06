<?php

namespace CarmineMM\QueryCraft\Adapter;

use CarmineMM\QueryCraft\Data\Model;
use CarmineMM\QueryCraft\Data\CarryOut;
use CarmineMM\QueryCraft\DB;
use CarmineMM\QueryCraft\Mapper\Entity;
use CarmineMM\QueryCraft\Mapper\TempEntity;
use DateTime;
use DateTimeZone;
use Exception;
use InvalidArgumentException;

abstract class SQLBaseDriver extends CarryOut
{
    /**
     * Prepara la instancia del SQL
     *
     * @param string $type
     * @return static
     */
    protected function instance(string $type = ''): static
    {
        if ($this->sql === '') {
            $this->sql = str_replace('{table}', $this->model->getTable(), $this->layout[$type]);
        }

        return $this;
    }

    /**
     * Execute a manually query
     *
     * @param string $query
     * @return mixed The return can be conditioned to the required query
     */
    public function query(string $query): mixed
    {
        $this->sql = $query;

        return $this->exec();
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
        foreach ($this->layout as $key => $value) {
            if (str_contains($value, 'WHERE')) {
                $this->layout[$key] = $three
                    ? str_replace('{where}', "AND {$column} {$sentence} {$three} {where}", $value)
                    : str_replace('{where}', "AND {$column} = '{$sentence}' {where}", $value);
            } else {
                $this->layout[$key] = $three
                    ? str_replace('{where}', "WHERE {$column} {$sentence} {$three} {where}", $value)
                    : str_replace('{where}', "WHERE {$column} = '{$sentence}' {where}", $value);
            }
        }

        return $this;
    }

    /**
     * Where clause for the query
     *
     * @param string $column
     * @param string $sentence
     * @param string $three
     * @return static
     */
    public function orWhere(string $column, string $sentence, string $three = ''): static
    {
        foreach ($this->layout as $key => $value) {
            if (str_contains($value, 'WHERE')) {
                $this->layout[$key] = $three
                    ? str_replace('{where}', "OR {$column} {$sentence} {$three} {where}", $value)
                    : str_replace('{where}', "OR {$column} = '{$sentence}' {where}", $value);
            } else {
                $this->layout[$key] = $three
                    ? str_replace('{where}', "OR WHERE {$column} {$sentence} {$three} {where}", $value)
                    : str_replace('{where}', "OR WHERE {$column} = '{$sentence}' {where}", $value);
            }
        }

        return $this;
    }

    /**
     * Get elements of the table
     *
     * @param array|null $columns
     * @return array
     */
    public function get(array|null $columns = null): array
    {
        $this->instance('select');

        $this->sql = str_replace('{column}', implode(', ', $columns ?? $this->columns), $this->sql);

        return $this->exec();
    }

    /**
     * View the SQL query
     *
     * @param string $sentence ['select', 'insert', 'update', 'delete']
     * @return string
     */
    public function toSql($sentence = 'select'): string
    {
        if ($this->sql === '') {
            $this->instance($sentence);
        }

        $this->prepareSql();

        return $this->sql;
    }

    /**
     * All elements of the table
     *
     * @param array|null $columns
     * @return array
     */
    public function all(array|null $columns = null): array
    {
        return $this->setColumns($columns)->instance('select')->exec();
    }

    /**
     * Select instance
     *
     * @param array $columns
     * @return static
     */
    public function select(array $columns = ['*']): static
    {
        $this->instance('select')->setColumns($columns);

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
        $this->instance('select');

        $this->sql = !is_null($offset)
            ? str_replace('{limit}', "LIMIT {$limit} OFFSET {$offset}", $this->sql)
            : str_replace('{limit}', "LIMIT {$limit}", $this->sql);

        return $this;
    }

    /**
     * First element of the table,
     * and limit 1 in array
     *
     * @param array $column
     * @return mixed
     */
    public function first(array $column = ['*']): mixed
    {
        return $this
            ->setColumns($column)
            ->instance('select')
            ->limit(1)
            ->exec()[0] ?? null;
    }

    /**
     * Create a new element in BD,
     * Using the fillable fields
     *
     * @param array $values
     * @return static
     */
    public function creator(array|Entity $values, Model $model): static
    {
        $this->instance('insert');
        $insertFields = $model->getFillable();
        $prepareItems = [];

        // Identificar si el modelo contiene un return type del tipo Entity
        if (is_string($model->getReturnType())) {
            $tempEntity = new TempEntity($model);
            $isEntity = !is_array($values);

            if ($isEntity) {
                $newValues = [];
                foreach ($model->getFillable() as $fillable) {
                    $newValues[$fillable] = $values->{$fillable};
                }
                $prepareItems = $newValues;
            } else {
                $prepareItems = $values;
            }

            $values = $tempEntity->setCasts(
                $isEntity
                    ? $values->getCasts()
                    : $tempEntity->getCasts()
            )
                ->setAttributes($prepareItems)
                ->getSetterCasts()
                ->getAttributes();
        }

        // Verificar si se tienen que insertar fields
        if ($this->model->hasTimestamps()) {
            $date = (new DateTime())
                ->setTimezone(new DateTimeZone(DB::getTimezone()))
                ->format('Y-m-d H:i:s');

            if ($createdField = $this->model->getCreatedAtField()) {
                $values[$createdField] = $date;
                $insertFields[] = $createdField;
            }

            if ($updatedField = $this->model->getUpdatedAtField()) {
                $values[$updatedField] = $date;
                $insertFields[] = $updatedField;
            }
        }

        $placeholder = [];
        $keys = [];

        foreach ($values as $key => $value) {
            $keys[] = $key;
            $placeholder[] = ':' . $key;
            $this->data[$key] = Sanitizer::string($value);
        }

        $this->sql = strtr($this->sql, [
            '{keys}' => implode(', ', $keys),
            // Set Placeholders
            '{values}' => implode(',', $placeholder),
        ]);

        return $this;
    }

    /**
     * Inserta datos a gran escala, no realiza mappers ni timestamps
     *
     * @param array $data
     * @return array
     */
    public function insert(array $data): array
    {
        $this->instance('insert');

        $placeholder = [];
        $columns = array_keys(reset($data));

        foreach ($data as $item) {
            // Aseguramos que cada item sea un array y tenga las mismas claves que el primero
            if (!is_array($item) || array_keys($item) !== $columns) {
                // Los items mandados no corresponden con el formato esperado
                throw new InvalidArgumentException("All items in \$data must have the same keys as the first item.");
            }

            $placeholdersRow = implode(', ', array_fill(0, count($columns), '?')); // Genera '?, ?, ?'
            $valuePlaceholders[] = "({$placeholdersRow})"; // Genera '(?, ?, ?)'

            // Recopila los valores para los parámetros de PDO
            foreach ($columns as $column) {
                $allParams[] = $item[$column] ?? null;
            }
        }

        $this->instance('insert');

        $this->sql = strtr($this->sql, [
            '{keys}' => implode(', ', $columns),
            // Set Placeholders
            '({values})' => implode(',', $valuePlaceholders),
        ]);

        return $this->unsafeExec($allParams);
    }

    /**
     * Create a new element in BD
     *
     * @param array|Entity $values
     * @param Model $model
     * @return array
     */
    public function create(array|Entity $values, Model $model): array
    {
        return $this->creator($values, $model)->exec($this->data);
    }

    /**
     * Delete element
     *
     * @return array
     */
    public function delete(): array
    {
        // Verificar si la instancia de delete no ha cambiado
        if ($this->layout['delete'] === 'DELETE FROM {table} {where}' && !$this->model->allow_bulk_delete) {
            // Estas lanzando un DELETE sobre una tabla sin antes delimitar por un 'WHERE'
            // No es necesario aclarar que esto podría eliminar todos los datos de su tabla.
            // Si quieres permitir esta acción, habilita el allow_bulk_delete en tu modelo o el DB::allowBulkDelete()
            throw new Exception("Your delete doesn't have a Where! 😢", 500);
        }

        // TODO: Verificar que el modelo tiene un deleted at
        $this->instance('delete');

        return $this->exec();
    }

    /**
     * Update element
     *
     * @param array $data
     * @return array
     */
    public function updatable(array $data = []): array
    {
        $this->instance('insert');

        return $this->data;
    }
}
