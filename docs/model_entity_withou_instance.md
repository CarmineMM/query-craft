# Model and Entity without instance

Models and entities can be used without instances, in case you want to use them quickly, you can use them as follows:

```php
use CarmineMM\QueryCraft\Data\Model;

// Especifica la conexión
$model = new Model('my-connection');

// Especifica la tabla
$model->setTable('products');

// La consulta
$data = $model->all();
```

## Your model quickly settings

```php
use CarmineMM\QueryCraft\Data\Model;

$model = new Model();

// Your table
$model->setTable('products');

// Configura tu conexión
$model->setConnection('my_connection_2');

// Set fillable fields
$model->setFillable([
    'name',
    'email',
    'password',
]);

// Set hidden fields
$model->setHidden([
    'password',
]);

// Set primary key
$model->setPrimaryKey('id');

$data = $model->all();
```
