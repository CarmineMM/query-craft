# Using the model

The model is the main route to interact with your database tables.It allows you to obtain, create, update and delete records in a fluid and intuitive way.

> **Security Note:** QueryCraft automatically encloses column and table names (e.g. `name` is converted to `"name"`) to prevent conflicts with SQL reserved words and improve compatibility between different database engines.

### Obtaining data

Like other ORMs, you have the option to build queries using the Model.

#### Obtaining all records

```php
// Obtains all columns of all records
$data = $model->all();

// Obtains only the specified columns
$data = $model->all(['id', 'name']);
```

#### Filtering results with `where`

You can chain `where` clauses to build more complex queries.

```php
// Simple search: WHERE name = 'John'
$data = $model->where('name', 'John')->all();

// Using other operators: WHERE name LIKE 'John%'
$data = $model->where('name', 'LIKE', 'John%')->all();

// Chaining clauses: WHERE status = 'active' AND age > 30
$data = $model->where('status', 'active')->where('age', '>', 30)->all();

// Using `orWhere`: WHERE status = 'active' OR is_premium = true
$data = $model->where('status', 'active')->orWhere('is_premium', true)->all();
```

#### Obtaining the first record
Recover the first record that matches the consultation.

```php
$data = $model->where('name', 'John')->first();
```

#### Checking null values

```php
// WHERE deleted_at IS NULL
$data = $model->whereNull('deleted_at')->all();

// WHERE processed_at IS NOT NULL
$data = $model->whereNotNull('processed_at')->all();
```

#### Limiting the number of records

```php
// Obtains the first 10 records
$data = $model->limit(10)->all();

// Obtains 10 records, omitting the first 20 (pagination)
$data = $model->limit(10, 20)->all();
```

#### Counting records

```php
// Counts all records in the table
$total = $model->count();

// Counts records that meet a condition
$activeUsers = $model->where('status', 'active')->count();
```

### Debugging with `toSql`

You can view the SQL query that will be generated before executing it.

```php
$sql = $model->where('name', 'LIKE', 'John%')->toSql();
// SELECT * FROM "users" WHERE "name" LIKE ?
```

## Creating records

Simple creation using an array.

```php
$data = $model->create([
    'name' => 'John',
    'email' => 'john@example.com',
]);
```

Creation using an `Entity`. The advantage is that you can take advantage of the `casts` defined in your entity.

```php
$data = $model->create(new Entity([
    'name' => 'John',
    'email' => 'john@example.com',
]));
```

## Updating records

To update records, first you must specify which rows to modify using a `where` clause.

```php
$model->where('id', 1)->update([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
]);
```

## Deleting records

By default, mass deletions are not allowed for security reasons. You must always specify a `where` condition.

```php
// Deletes the user with id = 1
$model->where('id', 1)->delete();
```

If the model has `soft deletes` enabled, this method will update the `deleted_at` field instead of permanently deleting the record.

## Snapshots handling

The snapshots functionality allows you to save and restore the state of the query builder constructor, which is useful for building complex queries in a more readable way or for reusing query parts.

### Taking a Snapshot

Saves the current state of the query builder constructor to be able to return to it later.

```php
// Constructs a base query
$query = $model->where('status', 'active');

// Takes a snapshot of the current query
$query->takeSnapshot('active_users');

// Continúa modificando la consulta
$activeUsers = $query->where('last_login', '>', '2023-01-01')->all();
```

### Restoring a Snapshot

Restores the state of the query builder constructor to the state saved in a previous snapshot.

```php
// Restores the query to the state saved in the snapshot 'active_users'
$query->restoreSnapshot('active_users');

// Now you can build a new query from the saved state
$recentlyActiveUsers = $query->where('last_login', '>', '2023-06-01')->all();
```

### Using without name

If you don't specify a name for the snapshot, a default name will be used.

```php
// Takes a snapshot without a name
$model->where('status', 'active')->takeSnapshot();

// Restores the last snapshot without a name
$model->restoreSnapshot();
```

> **Note:** Snapshots are useful when you need to build variations of a base query without having to repeat the initial conditions.
