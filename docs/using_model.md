# Using model

The model can and should be used to obtain data from the database.
In turn create, update and delete.

### Simple data retrieval

```php
// Use to obtain all columns
$data = $model->all();

// Use to obtain only certain columns
$data = $model->all(['id', 'name']);
```

### Filter data

```php
$data = $model->where('name', 'John')->all(); // WHERE name = 'John'

$data = $model->where('name', 'LIKE', 'John%')->all(); // WHERE name LIKE 'John%'

$data = $model->where('name', '!=', 'John')->all(); // WHERE name != 'John'
```

### Get the first model

Will obtain the first record that complies with the sentence

```php
$data = $model->where('name', 'John')->first();
```

### Null and not null

```php
$data = $model->whereNull('name')->all();

$data = $model->whereNotNull('name')->all();
```

### Limiting the amount of records

```php
$data = $model->limit(10)->all();
```

### Limiting the amount of records with offset

```php
$data = $model->limit(limit: 10, offset: 20)->all();
```

## Creating elements

Simple creation using an arrangement

```php
$data = $model->create([
    'name' => 'John',
    'email' => 'john@example.com',
]);
```

Creation using an entity.
The advantage of using the entity is that you can access its casts.

```php
$data = $model->create(new Entity([
    'name' => 'John',
    'email' => 'john@example.com',
]));
```

## Deleting elements

```php
$data = $model->delete();
```
