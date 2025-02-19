<?php

namespace CarmineMM\QueryCraft\Draft;

use CarmineMM\QueryCraft\Mapper\Entity;
use DateTime;

class UserEntity extends Entity
{
    public int $id;
    public string $name;
    public string $email;
    public ?string $email_verified_at;
    public string $password;
    public ?string $remember_token;
    public string $identification;
    public string $balance;
    public string|null $allow_login = null;
    public string $lang;
    public DateTime|string $created_at;
    public $updated_at;
    public $deleted_at;
}
