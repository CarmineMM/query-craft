# Installation and config

```bash
composer require carminemm/query-craft
```

## The settings work with single pattern

You can have an instance anywhere in your code.

```php
use CarmineMM\QueryCraft\Connection;

Connection::connect(config: [
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'username' => 'root',
    'password' => '',
    'database' => '',
    'options' => [], // PDO options
]);
```

## Multiple connections

```php
use CarmineMM\QueryCraft\Connection;

Connection::connect(
    name: 'config-1',
    config: [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => '',
        'database' => '',
        'options' => [], // PDO options
    ]
);

// You can pass the parameters by reference and not by name
// The first parameter being the name of the connection
Connection::connect('config-2', [
    'driver' => 'pgsql',
    'host' => '127.0.0.1',
    'username' => 'root',
    'password' => '',
    'database' => '',
    'options' => [], // PDO options
]);
```

## Schema

```php
use CarmineMM\QueryCraft\Connection;

Connection::connect(
    name: 'config-1',
    config: [
        'driver' => 'sqlsrv',
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => '',
        'database' => '',
        'schema' => 'dbo', // <-- Schema
    ]
);
```

## Available drivers

Use the text chain syntax to specify the driver

| Driver | Database   |
| ------ | ---------- |
| pgsql  | PostgreSQL |
| mysql  | MySQL      |
| sqlsrv | SQL Server |
