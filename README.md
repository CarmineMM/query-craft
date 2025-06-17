# Query Craft

Query Craft is an ORM, ETL and Migrations based on Data Mapper pattern, able to connect to **MySQL**, **Postgres** and **SQL Server**.

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

# Documentación

-   [Introduction](docs/index.md)
-   [Installation and config](docs/install.md)
-   [Models and entities](docs/model_entity.md)
-   [Models without instance](docs/model_without_instance.md)
-   [Using Model](docs/using_model.md)
-   [Model digging deeper](docs/model_digging_deeper.md)
-   [Entity digging deeper](docs/entity_digging_deeper.md)
-   [Debug](docs/debug.md)
-   [ETL](docs/etl.md)
-   [Migrations](docs/migrations.md)
