# Models and entities

To interact with your database, QueryCraft uses two key concepts: ** models ** and ** entities **.Understanding its difference is essential to use the ORM effectively.

-   **Model (`Model`)**: It is your gateway to a specific table of the database.It is responsible for building and executing consultations (Select, Insert, Update, Delete).Think about him as the **consultation builder**.
-   **Entity (`entity`)**: It is an object -oriented representation of a **single row** of your table.It contains the properties that correspond to the columns and allows you to handle the data in a structured way, including type transformation (casting).

## Recommended model creation

The model contains the configuration to connect to the correct table and define its behavior.

```php
use CarmineMM\QueryCraft\Data\Model;
use App\Entities\UserEntity; // Be sure to import your entity

class UserModel extends Model
{
    /**
     * (Optional) Table name.
     * By default, it is deduced from the name of the class (UserModel -> users).
     * You can specify it manually if you do not follow the convention.
     */
    protected string $table = 'users';

    /**
     * (Optional) Database connection to use.
     * Uses the 'default' connection if not specified.
     */
    protected string $connection = 'default';

    /**
     * (Required) Fields that can be mass assigned.
     * For security, only the fields listed here will be inserted or updated
     * when using methods like `create()` or `update()`.
     */
    protected array $fillable = [
        'name',
        'email',
        'password',
        'address',
        'birthdate',
    ];

    /**
     * (Optional) Fields that will be hidden when converting the result to array or JSON.
     * Useful for sensitive information like passwords.
     */
    protected array $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * (Optional, recommended) Define the type of data that the queries will return.
     * Can be 'array', 'object' or the name of an Entity class.
     * Using an Entity is the recommended practice.
     */
    protected string $returnType = UserEntity::class;

    /**
     * (Optional) Primary key of the table. By default it is 'id'.
     */
    protected string $primaryKey = 'id';

    /**
     * (Optional) Enables automatic management of the
     * `created_at` and `updated_at` fields. By default it is `true`.
     */
    protected bool $timestamps = true;

    /**
     * (Optional) Enables soft deletes.
     * Requires a `deleted_at` column in the table. By default it is `false`.
     */
    protected bool $softDeletes = true;
}
```

## Recommended entity creation

The entity is a class that helps you have your structured data.
This can be used to make casts on the data obtained from the database.

### Before creating the entity you must configure your model

If you will use entities, you must configure your model to use them.

```php
    use CarmineMM\QueryCraft\Data\Model;

    class UserModel extends Model
    {
        protected string $returnType = Entity::class;
    }
```

    Now we can configure the entity that the model will use.

```php
    use CarmineMM\QueryCraft\Data\Entity;
    use DateTime;

    class UserEntity extends Entity
    {
        /**
         * (Obligatorio) Propiedades de la entidad.
         * Deben coincidir con las columnas de tu tabla.
         * Usa tipos de PHP para definir cómo se manejarán los datos.
         */
        public int $id;
        public ?string $name;
        public string $email;
        public ?string $email_verified_at;
        public ?string $password;
        public ?string $address;
        public ?DateTime $birthdate; // Se convertirá a DateTime gracias al cast
        public ?string $remember_token;
        public ?string $profile_photo_path;

        // Timestamps gestionados por el modelo.
        // Se castean a DateTime automáticamente si $timestamps está habilitado.
        public ?DateTime $created_at;
        public ?DateTime $updated_at;
        public ?DateTime $deleted_at;

        /**
         * (Opcional) Define las conversiones de tipo (casting).
         * Los timestamps (`created_at`, `updated_at`, `deleted_at`) se
         * convierten a 'datetime' automáticamente.
         */
        protected array $casts = [
            'birthdate' => 'datetime',
            'is_admin'  => 'boolean', // Ejemplo de otro cast
        ];
    }
```

## Using PHP 8+ Attributes for Entity Definition

QueryCraft supports PHP 8+ attributes for defining entity properties and their behavior in a more declarative way. This approach provides better IDE support and makes the code more self-documenting.

### Defining Entity with Attributes

```php
use CarmineMM\QueryCraft\Data\Entity;
use CarmineMM\QueryCraft\Attributes\Cast;
use DateTime;

class UserEntity extends Entity
{
    #[Cast('int')]
    public int $id;

    #[Cast('string')]
    public string $name;

    #[Cast('string')]
    public string $email;

    #[Cast('datetime')]
    public ?DateTime $email_verified_at;

    #[Cast('string')]
    public ?string $password;

    #[Cast('string')]
    public ?string $address;

    #[Cast('datetime')]
    public ?DateTime $birthdate;

    #[Cast('string')]
    public ?string $remember_token;

    #[Cast('string')]
    public ?string $profile_photo_path;

    // Timestamps are automatically cast to DateTime
    public ?DateTime $created_at;
    public ?DateTime $updated_at;
    public ?DateTime $deleted_at;
}
```

### Benefits of Using Attributes

1. **Type Safety**: PHP's type system ensures type safety when working with entity properties.
2. **Better IDE Support**: Get autocompletion and type checking in modern IDEs.
3. **Self-documenting**: The code clearly shows the intended type and behavior of each property.
4. **Reduced Boilerplate**: No need to maintain a separate `$casts` array.

### Supported Cast Types

When using the `#[Cast]` attribute, you can use the following types:

- `int` or `integer`: Casts to integer
- `string`: Casts to string
- `float` or `double`: Casts to float
- `bool` or `boolean`: Casts to boolean
- `datetime`: Casts to DateTime object
- `json`: Encodes/decodes JSON data
- `array`: Casts to array

### Combining with Model Configuration

Attributes work alongside the model's configuration. If you define both, the attribute will take precedence over the model's `$casts` array.

```php
// In your model
protected array $casts = [
    'birthdate' => 'date', // This will be overridden by the attribute
    'is_admin' => 'boolean', // This will still work
];
```

### Example with All Features

```php
use CarmineMM\QueryCraft\Data\Entity;
use CarmineMM\QueryCraft\Attributes\Cast;
use DateTime;

class ProductEntity extends Entity
{
    #[Cast('int')]
    public int $id;

    #[Cast('string')]
    public string $name;

    #[Cast('float')]
    public float $price;

    #[Cast('bool')]
    public bool $in_stock = false;

    #[Cast('json')]
    public array $specifications = [];

    #[Cast('datetime')]
    public ?DateTime $created_at = null;

    // This property won't be included in toArray() or toJson()
    protected array $hidden = ['internal_notes'];
}
```

### Use the models and entities quickly

You may not have so much configuration, when using fast models you can omit the entity.
Check that documentation below.

-   [Models without instance](./model_without_instance.md)
-   [Entity digging deeper](./entity_digging_deeper.md)
