# Models and entities

Create your models to have direct connections to the database.
Generate entities to have control over the code and the entity obtained from the database.

## Recommended model creation

You can omit certain configurations, below are the most useful and that you can use in your daily models.

```php
use CarmineMM\QueryCraft\Data\Model;

class UserModel extends Model
{
    // (Optional)
    // By default the name of the table is determined automatically,
    // Based on the name of the model (The 'Model' is omitted in his name), in lowercase and adding an 's' at the end.
    // In this case you will look for the 'users' table, but you can customize the name of the table below
    protected string $table = '';

    // (Optional)
    // Hidden fields brought from the database
    // These will not map, they will not be executed in the Mapper of the entity.
    protected array $hidden = [];

    // (Obligatorio)
    // Fields that can be filled in the database
    protected array $fillable = [];

    // (Optional)
    // Connection name, by default it is 'default'
    protected string $connection = 'default';

    // (Optional, recommended to use entity)
    // Indicates the return value that SQL queries have
    // You can also indicate the Mapper to make insertions in the database
    // Valores posibles: object | array | Entity::class
    protected string $returnType = 'array';

    // (Optional)
    // Primary key, by default it is 'id'
    protected string $primaryKey = 'id';
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

class User extends Entity
{
    // (Mandatory)
    // Use it for retrospective mapping, points to the model associated with the
    public string|Model $model = UserModel::class;

    // (Mandatory)
    // Then define the fields of the entity
    // These should agree with the fields of the database
    public int $id;
    public ?string $name;
    public string $email;
    public ?string $email_verified_at;
    public ?string $password;
    public string $address;
    public DateTime $birthdate;
    public ?string $remember_token;
    public ?string $profile_photo_path;

    // if your model Uses Timestamps, You Can defines Them Here
    // Your default database can have them in Null, they can also come as a string
    // But they finally transform into Datetime
    public DateTime|string|null $created_at = null;
    public DateTime|string|null $updated_at = null;
    public DateTime|string|null $deleted_at = null;

    // (optional)
    // Indicate the fields to be cast
    // By default the timestamps are married
    protected array $casts = [
        'birthdate' => 'datetime',
    ];

    // (Optional)
    // Puedes definir cualquier cantidad de métodos o propiedades adicionales
    // ...
}
```
