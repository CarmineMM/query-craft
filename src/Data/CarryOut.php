<?php

namespace CarmineMM\QueryCraft\Data;

use CarmineMM\QueryCraft\Connection;

class CarryOut
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
    private function prepareSql(): static
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

        $query = $this->pdo->prepare($this->sql);

        try {
            $query->execute($params);
        } catch (\Throwable $th) {
            throw new \Exception("Error Execute Query: " . $query->errorInfo()[2], 500, $th);
        }

        // Devolver resultado del SELECT
        if (strpos($this->sql, 'SELECT') !== false) {
            $returnType = $this->model->getReturnType();
            $data = in_array($returnType, [\PDO::FETCH_ASSOC, \PDO::FETCH_OBJ])
                ? $query->fetchAll($returnType)
                : $query->fetchAll(\PDO::FETCH_CLASS, $returnType);
        }


        $this->sql = '';

        if (Connection::$instance->debug) {
            // End time
            $time = microtime(true) - $startTime;
            $memory = memory_get_usage() - $startMemory;
        }

        return $data;
    }

    /**
     * Obtain the generated SQL
     */
    public function toSQL(): string
    {
        $this->prepareSql();

        return $this->sql;
    }

    /**
     * Rest the SQL
     */
    public function reset(): static
    {
        $this->sql = '';

        return $this;
    }
}
