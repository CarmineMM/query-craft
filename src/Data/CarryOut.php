<?php

namespace CarmineMM\QueryCraft\Data;

use CarmineMM\QueryCraft\Cache;
use CarmineMM\QueryCraft\DB;
use CarmineMM\QueryCraft\Debug;

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
     * Data to insert o update
     *
     * @var array
     */
    protected array $data = [];

    /**
     * Layouts para las query's
     *
     * @var array
     */
    protected array $layout = [
        'select' => 'SELECT {column} {innerQuery} FROM {table} {where} {group} {order} {limit} {offset}',
        'insert' => 'INSERT INTO {table} ({keys}) VALUES ({values})',
        'update' => 'UPDATE {table} SET {set} {where}',
        'delete' => 'DELETE FROM {table} {where}',
    ];

    /**
     * Where clauses
     *
     * @var array
     */
    protected array $wheres = [];

    /**
     * All bindings for the query
     *
     * @var array
     */
    protected array $bindings = [];

    /**
     * Snapshot of the query builder state
     *
     * @var array
     */
    protected array $snapshots = [];

    /**
     * Conexión por PDO
     *
     * @var \PDO
     */
    protected \PDO $pdo;

    /**
     * Prepares the SQL query by compiling all its parts.
     * Each driver is responsible for implementing this method.
     *
     * @return static
     */
    abstract protected function prepareSql(): static;

    /**
     * Take a snapshot of the current query builder state.
     *
     * @param string|null $name The name of the snapshot.
     * @return static
     */
    public function takeSnapshot(?string $name = null): static
    {
        $this->snapshots[$name ?? 'default'] = [
            'columns'  => $this->columns,
            'wheres'   => $this->wheres,
            'bindings' => $this->bindings,
        ];

        return $this;
    }

    /**
     * Restore the query builder state from a snapshot.
     *
     * @param string|null $name The name of the snapshot to restore.
     * @return static
     */
    public function restoreSnapshot(?string $name = null): static
    {
        $snapshot = $this->snapshots[$name ?? 'default'] ?? null;

        if ($snapshot) {
            $this->columns  = $snapshot['columns'];
            $this->wheres   = $snapshot['wheres'];
            $this->bindings = $snapshot['bindings'];
        }

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
     * Execute the query
     */
    protected function exec(array $params = []): array
    {
        if (DB::getDebugMode()) {
            $startTime = microtime(true);
            $startMemory = memory_get_usage();
        }

        $this->prepareSql();

        // Verificar si la consulta existe en cache
        // No guardar consultar que sean muy largas
        if ($this->model->hasCache() && strlen($this->sql) < 60 && Cache::has($this->sql . json_encode($this->bindings))) {
            $get = Cache::get($this->sql . json_encode($this->bindings));

            if (DB::getDebugMode()) {
                $endtime = microtime(true) - $startTime;

                // End time
                Debug::addQuery([
                    'query' => $this->sql,
                    'time' => $endtime,
                    'ms' => round($endtime * 1000, 3) . ' ms',
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
            $query->execute(array_merge($this->bindings, $params));
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
                $data = array_map(fn($item) => new $returnType($item, $this->model), $data);
            }

            if ($this->model->hasCache() && strlen($this->sql) < 60) {
                Cache::set($this->sql . json_encode($this->bindings), $data);
            }
        }
        // Si es de tipo create
        else if (strpos($this->sql, 'INSERT') !== false) {
            $data = $this->data;
            $data[$this->model->getPrimaryKey()] = $this->pdo->lastInsertId();
        }
        // Consulta DELETE
        else if (strpos($this->sql, 'DELETE') !== false) {
            // Número de filas afectadas
            $data = ['affected_rows' => $query->rowCount()];

            // Invalidate cache related to deleted data
            if ($this->model->hasCache()) {
                Cache::invalidateByTable($this->model->getTable());
            }
        }

        if (DB::getDebugMode()) {
            $endtime = microtime(true) - $startTime;

            // End time
            Debug::addQuery([
                'query' => $this->sql,
                'time' => $endtime,
                'ms' => round($endtime * 1000, 3) . ' ms',
                'memory' => memory_get_usage() - $startMemory,
                'connection' => $this->model->getConnection(),
                'cache' => false,
            ]);
        }

        $this->reset();

        return $data;
    }

    /**
     * Ejecución sin attach sobre la data
     *
     * @param array $params
     * @return array
     */
    protected function unsafeExec(array $params = []): array
    {
        $this->prepareSql();

        $query = $this->pdo->prepare($this->sql);

        try {
            $query->execute(array_merge($this->bindings, $params));
        } catch (\Throwable $th) {
            // Consultation execution error
            // If you see this error, it is because there is an error in the SQL consultation.
            throw new \Exception("Error Execute Query: " . $query->errorInfo()[2], 500, $th);
        }

        $this->reset();

        return $params;
    }

    /**
     * Resets the query builder state to its initial values.
     */
    public function reset(): static
    {
        $this->sql = '';
        $this->columns = ['*'];
        $this->wheres = [];
        $this->bindings = [];
        $this->layout =  [
            'select' => 'SELECT {column} {innerQuery} FROM {table} {where} {group} {order} {limit} {offset}',
            'insert' => 'INSERT INTO {table} ({keys}) VALUES ({values})',
            'update' => 'UPDATE {table} SET {set} {where}',
            'delete' => 'DELETE FROM {table} {where}',
        ];
        $this->data = [];

        return $this;
    }

    /**
     * Adds a value to the bindings array.
     *
     * @param mixed $value The value to bind.
     * @return void
     */
    protected function addBinding($value): void
    {
        if (is_array($value)) {
            $this->bindings = array_merge($this->bindings, $value);
        } else {
            $this->bindings[] = $value;
        }
    }
}
