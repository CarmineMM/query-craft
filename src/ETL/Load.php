<?php

namespace CarmineMM\QueryCraft\ETL;

use CarmineMM\QueryCraft\Data\Model;
use CarmineMM\QueryCraft\Facades\DB;

class Load
{
    /**
     * Truncate the table before inserting
     *
     * @var boolean
     */
    private bool $truncate = false;

    /**
     * Load constructor.
     *
     * @param Model $toModel
     */
    public function __construct(
        public Model $toModel,
    ) {
        //
    }

    /**
     * Truncate table?
     *
     * @param boolean $truncate
     * @return static
     */
    public function truncate(bool $truncate = true): static
    {
        $this->truncate = $truncate;
        return $this;
    }

    /**
     * Insert de data
     *
     * @param array $data
     * @return array
     */
    public function insert(array $data): array
    {
        // Truncate table before insert
        if ($this->truncate) {
            DB::driver($this->toModel->getDriver())
                ->truncate($this->toModel->getTable());
        }

        return $this->toModel->insert($data);
    }
}
