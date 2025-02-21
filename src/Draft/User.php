<?php

namespace CarmineMM\QueryCraft\Draft;

use AllowDynamicProperties;
use CarmineMM\QueryCraft\Data\Model;

class User extends Model
{
    protected string $returnType = UserEntity::class;

    protected array $hidden = [
        // 'password',
    ];
}
