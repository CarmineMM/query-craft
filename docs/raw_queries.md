# Raw SQL Queries

QueryCraft provides the ability to execute raw SQL queries when you need more control over your database operations. This feature is particularly useful for complex queries that are difficult or impossible to express using the query builder.

## Basic Usage

### Setting a Query

Use the `query()` method to set a raw SQL query. This method does not execute the query immediately; it only stores it for later execution.

```php
$model = new YourModel();
$model->query('SELECT * FROM users WHERE status = :status');
```

### Executing the Query

After setting the query with `query()`, you can execute it using the `exec()` method:

```php
$result = $model->exec();
```

The `exec()` method returns `true` on success or `false` on failure.

## Example: Select Query with Parameters

```php
$model = new User();

// Set the query with named parameters
$model->query('SELECT * FROM users WHERE status = :status AND created_at > :date');

// Execute with parameter bindings
$result = $model->exec([
    'status' => 'active',
    'date' => '2023-01-01'
]);

// The result will be true if the query executed successfully
```

## Example: Insert with Parameters

```php
$model = new User();

// Set the insert query with named parameters
$model->query('INSERT INTO users (name, email, status) VALUES (:name, :email, :status)');

// Execute with parameter bindings
$result = $model->exec([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'status' => 'active'
]);
```

## Example: Update with Parameters

```php
$model = new User();

// Set the update query with named parameters
$model->query('UPDATE users SET status = :status, updated_at = NOW() WHERE id = :id');

// Execute with parameter bindings
$result = $model->exec([
    'status' => 'inactive',
    'id' => 42
]);
```

## Example: Delete with Parameters

```php
$model = new User();

// Set the delete query with named parameters
$model->query('DELETE FROM users WHERE id = :id');

// Execute with parameter binding
$result = $model->exec([
    'id' => 42
]);
```

## Error Handling

If you try to call `exec()` without first setting a query with `query()`, a `RuntimeException` will be thrown:

```php
try {
    $model = new User();
    $result = $model->exec(); // This will throw an exception
} catch (\RuntimeException $e) {
    echo $e->getMessage(); // Outputs: No query has been set. Use the query() method to set a raw SQL query.
}
```

## Security Considerations

When using raw SQL queries, you are responsible for ensuring that your queries are secure:

1. **Use Parameter Binding**: Always use parameterized queries to prevent SQL injection.
2. **Validate Input**: Validate all user input before using it in your queries.
3. **Least Privilege**: Ensure your database user has only the necessary permissions.

## Limitations

- The `exec()` method returns a boolean indicating success or failure. If you need to retrieve data from a SELECT query, you should use the query builder methods instead.
- Transactions should be handled at the PDO level if needed.

## Best Practices

1. **Use Query Builder When Possible**: Prefer using the query builder methods for better security and maintainability.
2. **Keep Complex Queries in Models**: Place complex raw queries in your model methods to keep your controllers clean.
3. **Document Your Queries**: Add comments explaining complex queries for future maintainers.

## See Also

- [Using the Model](using_model.md)
- [Query Builder](query_builder.md)
- [Database: Getting Started](database.md)
