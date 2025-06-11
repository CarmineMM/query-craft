<?php

namespace CarmineMM\QueryCraft\Migration\Contracts;

use CarmineMM\QueryCraft\Migration\Blueprint;

interface Grammar
{
    /**
     * Compile a create table command.
     *
     * @param  \CarmineMM\QueryCraft\Migration\Blueprint  $blueprint
     * @return string
     */
    public function compileCreate(Blueprint $blueprint): string;
}
