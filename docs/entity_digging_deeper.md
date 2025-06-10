# Entity digging deeper

Know how to create custom Casts, possible types of casts and how to better use the entity.

### Possible casts (Predefined)

There are predefined casts that you can use in the entity.Both when you bring new records and when you create or update records.

```php
protected array $casts = [
    'field' => 'json',
    'field' => 'object',
    'field' => 'datetime',
    'field' => 'array',
];
```

## Create your own Casts

Use the methods get for when you bring data from the database and set for when you create or update records.

```php
namespace App\Casts;

use CarmineMM\QueryCraft\Casts\Cast;

class MyCast implements Cast
{
    public function get($value)
    {
        return $value;
    }

    public function set($value)
    {
        return $value;
    }
}
```

## Array attributes fillable

Converts your entity into an array that you can use (Applies the Casts and the Hidden of your model)

```php
$user->toArray();
```
