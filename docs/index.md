# Query Craft

Query Craft is an ORM based on Data Mapper pattern, able to connect to **MySQL**, **Postgres** and **SQL Server**.
A Extractor-Transformer-Loader (ETL) is also at your disposal.

## Installation

```bash
composer require carminemm/query-craft
```

## Usage

```php
use CarmineMM\QueryCraft\Facades\Connection;
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
