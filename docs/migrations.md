# Database Migrations

This document explains how to use the database migration system to manage your database schema.

## Introduction

Migrations are like version control for your database, allowing your team to define and share the application's database schema definition. They're an essential part of any application that works with a database.

## Creating a Table

To create a new database table, you can use the `Schema::create` method. This method accepts two arguments: the name of the table and a `Closure` which receives a `Blueprint` object that you may use to define the new table.

### Example

Here is a basic example of how to create a `users` table:

```php
use CarmineMM\QueryCraft\Migration\Blueprint;
use CarmineMM\QueryCraft\Migration\Schema;

Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamps();
});
```

## Available Column Types

The schema builder provides a variety of column types that you may use when building your tables:

| Command                                    | Description                                                    |
| ------------------------------------------ | -------------------------------------------------------------- |
| `$table->id();`                            | Create Auto-incrementing column "id" UNSIGNED INTEGER. (PK)    |
| `$table->increments('id');`                | Auto-incrementing UNSIGNED INTEGER (PK).                       |
| `$table->increments('id', 'small');`       | Auto-incrementing UNSIGNED SMALLINT (PK).                      |
| `$table->integer('votes');`                | INTEGER equivalent.                                            |
| `$table->tinyInteger('votes');`            | TINYINT equivalent.                                            |
| `$table->smallInteger('votes');`           | SMALLINT equivalent.                                           |
| `$table->mediumInteger('votes');`          | MEDIUMINT equivalent.                                          |
| `$table->bigInteger('votes');`             | BIGINT equivalent.                                             |
| `$table->string('name', 100);`             | VARCHAR equivalent with a default length of 255.               |
| `$table->text('description');`             | TEXT equivalent.                                               |
| `$table->mediumText('description');`       | MEDIUMTEXT equivalent.                                         |
| `$table->longText('description');`         | LONGTEXT equivalent.                                           |
| `$table->enum('level', ['easy', 'hard']);` | ENUM equivalent.                                               |
| `$table->timestamp('added_on');`           | TIMESTAMP equivalent.                                          |
| `$table->timestamps();`                    | Adds nullable `created_at` and `updated_at` TIMESTAMP columns. |

## Column Modifiers

In addition to the column types listed above, there are several column "modifiers" you may use while adding a column to a database table. For example, to make a column "nullable", you may use the `nullable` method:

```php
$table->string('email')->nullable();
```

Below is a list of all the available column modifiers.

| Modifier       | Description                                        |
| -------------- | -------------------------------------------------- |
| `->nullable()` | Allows NULL values to be inserted into the column. |
| `->unsigned()` | Set INTEGER columns as UNSIGNED (MySQL only).      |
| `->unique()`   | Adds a "unique" index.                             |

## Database Configuration

Before running migrations, you need to configure your database connection using the `Connection::connect` method.

```php
use CarmineMM\QueryCraft\Facades\Connection;

Connection::connect('default', [
    'driver'   => 'mysql', // 'mysql', 'pgsql', or 'sqlsrv'
    'host'     => '127.0.0.1',
    'database' => 'my_database',
    'username' => 'root',
    'password' => '',
]);
```
