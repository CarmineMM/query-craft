# Query Craft ETL

Query Craft offers an ETL to connect data between databases.

### Configurar ambas conexiones

```php
use CarmineMM\QueryCraft\Connection;

Connection::setConnection('primary_connection', [
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'port' => '3306',
    'database' => 'database',
    'username' => 'root',
    'password' => '',
]);

Connection::setConnection('secondary_connection', [
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'port' => '3306',
    'database' => 'database',
    'username' => 'root',
    'password' => '',
]);
```

### Using the ETL Factory

Specify the tables from which you are going to extract the data and where you are going to insert them.

```php
use CarmineMM\QueryCraft\ETL\Factory;

$etl = new Factory(
    from: 'users_from_table',
    to: 'destiny_table',
);
```

You can also have model instances and pass them to the factory.
In this case we will use the Quickly Models.

```php
use CarmineMM\QueryCraft\ETL\Factory;
use CarmineMM\QueryCraft\Data\Model;


$etl = new Factory(
    from: (new Model('primary_connection'))->setTable('users_from_table'),
    to: (new Model('secondary_connection'))->setTable('destiny_table'),
);
```

## Configure the fields to make extraction and transformation

```php
$etl->extractAttributes([
    // Define the fields as they come and where they go
    // from => to
    'from_field' => 'to_field',
    'from_field2' => 'to_field_2'

    // You can also specify better the transformation
    // to => value
    fn($data) => ['to_field_3', $data['from_field_3']],
    fn($data) => ['to_field_3', $data['other_field'] * 2], // You can execute PHP code without problems

    // Processed values
    // to => value
    function($data) {
        return ['to_field_4', $data['other_filed'] + $data['other_field_2']];
    },
]);
```

## Process ETL

```php
$etl->processEtl(true); // Force the debug mode
```

## Considerations

### ETL process with functions

The extraction and transformation process using functions can deteriorate the optimization, better avoid them in the possible.

### A lot of SQL load

Sometimes the amount of data inserted exceeds the allowed amount of the SQL.In this case, you can use the `chunkSize` parameter to limit the number of records inserted in each iteration.

```php
$etl = new Factory(
    from: (new Model('primary_connection'))->setTable('users_from_table'),
    to: (new Model('secondary_connection'))->setTable('destiny_table'),

    chunkSize: 4_000 // <-- Get the elements in batches of 4000
);
```
