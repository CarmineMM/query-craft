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
     * @return void
     */
    public function insert(array $data)
    {
        $this->toModel->insert($data);
    }
}
