<?php

use CarmineMM\QueryCraft\Data\Model;

class Transform
{
    public function __construct(
        public Model $fromModel,
        public Model $toModel,
    ) {
        //
    }

    public function transform(array $pairAttributes) {}
}
