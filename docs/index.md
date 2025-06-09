# Query Craft

Query Craft is an ORM based on Data Mapper pattern, able to connect to **MySQL** and **Postgres**.

## Installation

```bash
composer require carminemm/query-craft
```

## Usage

```php
use CarmineMM\QueryCraft\Connection;
use CarmineMM\QueryCraft\Data\Model;

Connection::connect(config: [
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'username' => 'root',
    'password' => '',
    'database' => '',
    'options' => [], // PDO options
]);

$model = new Model('star_client');
$model->setTable('products');

$data = $model->all();
```
