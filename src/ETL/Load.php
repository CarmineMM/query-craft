<?php

namespace CarmineMM\QueryCraft\ETL;

use CarmineMM\QueryCraft\Data\Model;

class Load
{
    public function __construct(
        public Model $toModel,
    ) {
        //
    }

    /**
     * Insert de data
     *
     * @return array
     */
    public function insert(array $data): array
    {
        return $this->toModel->insert($data);
    }
}
