<?php

namespace Examples;

use CarmineMM\QueryCraft\Data\Model;
use CarmineMM\QueryCraft\Mapper\Entity;
use CarmineMM\QueryCraft\Attributes\Cast;

class UserEntity extends Entity
{
    #[Cast('int')]
    public int $id;

    #[Cast('string')]
    public string $username;

    #[Cast('string')]
    public string $email;

    #[Cast('string')]
    public string $password; // Se ocultará automáticamente si está en hidden

    // Float with 2 decimal places precision
    #[Cast('float', [2])]
    public float $price = 0.0;

    // Integer cast
    #[Cast('int')]
    public int $quantity = 0;

    // Float with 4 decimal places precision
    #[Cast('float', [4])]
    public float $discount = 0.0;

    #[Cast('datetime')]
    public \DateTime $createdAt;

    #[Cast('datetime')]
    public ?\DateTime $updatedAt = null;

    #[Cast('json')]
    public ?array $preferences = null;

    /**
     * @var string Nombre de la tabla en la base de datos
     */
    protected string $table = 'users';

    /**
     * Campos ocultos al convertir a array
     */
    protected array $hidden = ['password'];

    public function __construct(array $attributes = [], ?Model $model = null)
    {
        parent::__construct($attributes, $model);
    }
}
