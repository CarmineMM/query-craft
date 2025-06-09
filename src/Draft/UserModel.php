<?php

namespace CarmineMM\QueryCraft\Draft;

use CarmineMM\QueryCraft\Data\Model;

class UserModel extends Model
{
    protected string $returnType = User::class;

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
