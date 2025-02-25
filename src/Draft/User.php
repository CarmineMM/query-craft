<?php

namespace CarmineMM\QueryCraft\Draft;

use CarmineMM\QueryCraft\Data\Model;

class User extends Model
{
    protected string $returnType = UserEntity::class;

    protected array $fillable = [
        'name',
        'birthdate',
        'email',
        'address',
    ];

    protected array $hidden = [
        // 'password',
    ];
}
