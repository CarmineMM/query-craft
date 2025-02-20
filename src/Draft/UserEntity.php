<?php

namespace CarmineMM\QueryCraft\Draft;

use CarmineMM\QueryCraft\Mapper\Entity;
use DateTime;

class UserEntity extends Entity
{
    public int $id;
    public ?string $name;
    public string $email;
    public ?string $email_verified_at;
    public ?string $password;
    public string $address;
    public DateTime|string $birthdate;
    public ?string $remember_token;
    public ?string $profile_photo_path;
    public DateTime|string|null $created_at;
    public DateTime|string|null $updated_at;
    public DateTime|string|null $deleted_at;

    protected array $casts = [
        'name' => 'json',
    ];
}
