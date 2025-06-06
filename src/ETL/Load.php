<?php

namespace CarmineMM\QueryCraft\ETL;

use CarmineMM\QueryCraft\Data\Model;

class Load
{
    public function __construct(
        public Model $toModel,
        public array $data
    ) {
        //
    }
}
