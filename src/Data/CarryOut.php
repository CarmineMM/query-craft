<?php

namespace CarmineMM\QueryCraft\Data;

use CarmineMM\QueryCraft\Cache;
use CarmineMM\QueryCraft\Connection;
use CarmineMM\QueryCraft\Debug;
use CarmineMM\QueryCraft\Draft\UserEntity;
use CarmineMM\QueryCraft\Mapper\Entity;

abstract class CarryOut
{
    /**
     * Model
     *
     * @var Model
     */
    protected Model $model;

    /**
     * SQL generated
     */
    protected string $sql = '';

    /**
     * Columns for the SELECT request
     */
    protected array $columns = ['*'];

    /**
     * Layouts para las query's
     *
     * @var array
     */
    protected array $layout = [
        'select' => 'SELECT {column} {innerQuery} FROM {table} {where} {group} {order} {limit} {offset}',
        'insert' => 'INSERT INTO {table} ({keys}) VALUES ({values})',
        //'update' => 'UPDATE %s SET %s',
        'delete' => 'DELETE FROM {table} {where}',
    ];

    /**
     * Conexión por PDO
     *
     * @var \PDO
     */
    protected \PDO $pdo;

    /**
     * Prepares the Select
     *
     * @return static
     */
    protected function prepareSql(): static
    {
        $columns = implode(', ', $this->columns);
        $this->sql = str_replace('{column}', $columns, $this->sql);
        $this->sql = trim(
            str_replace(
                ['{innerQuery}', '{where}', '{group}', '{order}', '{limit}', '{offset}'],
                ['', '', '', '', '', ''],
                $this->sql
            )
        );

        return $this;
    }

    /**
     * Set Columns for the SELECT request
     *
     * @param array $columns
     * @return static
     */
    public function setColumns(array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Ejecutar la consulta
     */
    protected function exec(array $params = []): array
    {
        if (Connection::$instance->debug) {
            $startTime = microtime(true);
            $startMemory = memory_get_usage();
        }

        $this->prepareSql();

        // Verificar si la consulta existe en cache
        if ($this->model->hasCache() && Cache::has($this->sql)) {
            $get = Cache::get($this->sql);

            if (Connection::$instance->debug) {
                // End time
                Debug::addQuery([
                    'query' => $this->sql,
                    'time' => microtime(true) - $startTime,
                    'memory' => memory_get_usage() - $startMemory,
                    'connection' => $this->model->getConnection(),
                    'cache' => true,
                ]);
            }

            $this->reset();

            return $get;
        }

        $query = $this->pdo->prepare($this->sql);

        try {
            $query->execute($params);
        } catch (\Throwable $th) {
            // Consultation execution error
            // If you see this error, it is because there is an error in the SQL consultation.
            throw new \Exception("Error Execute Query: " . $query->errorInfo()[2], 500, $th);
        }

        // Devolver resultado del SELECT
        if (strpos($this->sql, 'SELECT') !== false) {
            $returnType = $this->model->getReturnType();

            $data = in_array($returnType, [\PDO::FETCH_ASSOC, \PDO::FETCH_OBJ])
                ? $query->fetchAll($returnType)
                : $query->fetchAll(\PDO::FETCH_ASSOC);

            // Si es una entidad, establecer la instancia
            if (is_string($returnType)) {
                $data = array_map(fn($item) => new $returnType($this->model, $item), $data);
            }

            if ($this->model->hasCache()) {
                Cache::set($this->sql, $data);
            }
        }

        if (Connection::$instance->debug) {
            // End time
            Debug::addQuery([
                'query' => $this->sql,
                'time' => microtime(true) - $startTime,
                'memory' => memory_get_usage() - $startMemory,
                'connection' => $this->model->getConnection(),
                'cache' => false,
            ]);
        }

        $this->reset();

        return $data;
    }

    /**
     * Rest the SQL
     */
    public function reset(): static
    {
        $this->sql = '';
        $this->columns = ['*'];
        $this->layout =  [
            'select' => 'SELECT {column} {innerQuery} FROM {table} {where} {group} {order} {limit} {offset}',
            'insert' => 'INSERT INTO {table} ({keys}) VALUES ({values})',
            //'update' => 'UPDATE %s SET %s',
            'delete' => 'DELETE FROM {table} {where}',
        ];

        return $this;
    }
}
