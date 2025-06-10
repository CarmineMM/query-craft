<?php

namespace CarmineMM\QueryCraft\Adapter;

use CarmineMM\QueryCraft\Data\Model;
use CarmineMM\QueryCraft\Data\CarryOut;
use CarmineMM\QueryCraft\Mapper\Entity;
use CarmineMM\QueryCraft\Mapper\Modeling;
use Exception;
use InvalidArgumentException;

/**
 * Drivers base para casi todas las bases de datos basadas en SQL
 *
 * @package CarmineMM\QueryCraft
 * @author Carmine Maggio <carminemaggiom@gmail.com>
 * @version 1.5.0
 */
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
        if ($this->model->getTable() === '') {
            throw new Exception("Table name is required!", 500);
        }

        if ($this->sql === '') {
            $table = $this->model->getSchema()
                ? "{$this->model->getSchema()}.{$this->model->getTable()}"
                : $this->model->getTable();

            $this->sql = str_replace('{table}', $table, $this->layout[$type]);
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
     * Where clause for the query.
     * TODO: Extender el Where para soportar más tipos de sentencias
     *
     * @param string $column
     * @param string $sentence
     * @param string $three
     * @return static
     */
    public function where(string $column, string $sentence, string $three = ''): static
    {
        // Almacenar el 'WHERE' en una propiedad para poder aplicarlo al SQL final
        // Esto es más robusto que modificar directamente `layout` en cada llamada.
        // Asumo que tienes una propiedad como `$this->whereClauses = []` en `CarryOut`
        // o que tu `prepareSql` manejará esto de otra forma más dinámica.
        // Por simplicidad en este ejemplo, asumo que `layout` contiene placeholders
        // para el 'WHERE' que se llenarán posteriormente.

        $whereClause = $three
            ? "{$column} {$sentence} {$three}"
            : "{$column} = '{$sentence}'";

        // Aquí deberías tener una forma más robusta de construir la cláusula WHERE.
        // Por ejemplo, concatenando las cláusulas WHERE en una propiedad del objeto,
        // y luego inyectándolas en `prepareSql`.
        // Para este ejemplo, lo dejo como lo tienes, pero ten en cuenta la mejora.
        foreach ($this->layout as $key => $value) {
            if (str_contains($value, 'WHERE')) {
                $this->layout[$key] = str_replace('{where}', "AND {$whereClause} {where}", $value);
            } else {
                // Si aún no hay WHERE, lo agregamos
                $this->layout[$key] = str_replace('{where}', "WHERE {$whereClause} {where}", $value);
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
        $whereClause = $three
            ? "{$column} {$sentence} {$three}"
            : "{$column} = '{$sentence}'";

        foreach ($this->layout as $key => $value) {
            if (str_contains($value, 'WHERE')) {
                $this->layout[$key] = str_replace('{where}', "OR {$whereClause} {where}", $value);
            } else {
                $this->layout[$key] = str_replace('{where}', "WHERE {$whereClause} {where}", $value);
            }
        }

        return $this;
    }

    /**
     * Where clause for the query
     *
     * @param string $column
     * @return static
     */
    public function whereNotNull(string $column): static
    {
        $this->where($column, 'IS NOT', 'NULL');

        return $this;
    }

    /**
     * Where clause for the query
     *
     * @param string $column
     * @return static
     */
    public function whereNull(string $column): static
    {
        $this->where($column, 'IS', 'NULL');

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
     * Countable elements
     *
     * @param string $column
     * @return int
     */
    public function count(string $column = '*'): int
    {
        $this->instance('select');

        $this->sql = str_replace('{column}', "COUNT({$column})", $this->sql);

        return (int) reset($this->exec()[0]);
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
        $values = $values instanceof Entity ? $values->toArray() : $values;

        $fillable_data = Modeling::fillableData($model, $values);

        ['values' => $insertValues] = Modeling::applyTimeStamps($model, $fillable_data);

        $placeholder = [];
        $keys = [];

        foreach ($insertValues as $key => $value) {
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
     * Insert large -scale data, do not make mappers or timestamp
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
    public function update(array|Entity $values, Model $model): array
    {
        $this->instance('update');

        $values = $values instanceof Entity ? $values->toArray() : $values;
        $fillable_data = Modeling::fillableData($model, $values);
        $update_data = Modeling::applyUpdatedAt($model, $fillable_data)['values'];

        // Construir la cláusula SET
        $setClauses = [];
        $params = [];
        foreach ($update_data as $key => $value) {
            $setClauses[] = "{$key} = :{$key}";
            $params[":{$key}"] = Sanitizer::string($value);
        }

        // Unir las cláusulas SET
        $set_string = implode(', ', $setClauses);

        // Reemplazar el placeholder {set} en la SQL
        $this->sql = str_replace('{set}', $set_string, $this->sql);

        // Una forma sencilla de verificar si el WHERE ha sido añadido
        // (esto depende de cómo manejes el placeholder {where} internamente)
        if (str_contains($this->sql, '{where}') && !$model->allow_bulk_update) {
            // Este {where} aún existe en la sentencia, significa que no se aplicó un WHERE
            throw new Exception("Your update doesn't have a Where clause! 😢 Bulk updates are not allowed by default. Enable allow_bulk_update in your model or add a where condition.", 500);
        }

        // La llamada a exec se encargará de preparar y ejecutar la consulta
        // y de eliminar el placeholder {where} si está vacío.
        $result = $this->exec($params);

        // Puedes devolver los datos actualizados junto con las filas afectadas.
        // `exec` debería devolver la información relevante (ej. affected_rows).
        // Si `exec` ya devuelve un array con `affected_rows`, simplemente retornarlo.
        return $result;
    }
}
