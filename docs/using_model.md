# Usando el Modelo

El modelo es la vía principal para interactuar con tus tablas de la base de datos. Te permite obtener, crear, actualizar y eliminar registros de una manera fluida e intuitiva.

> **Nota de Seguridad:** QueryCraft se encarga de entrecomillar automáticamente los nombres de las columnas y tablas (ej. `name` se convierte en `"name"`) para prevenir conflictos con palabras reservadas de SQL y mejorar la compatibilidad entre diferentes motores de base de datos.

### Obtención de Datos

#### Obtener todos los registros

```php
// Obtiene todas las columnas de todos los registros
$data = $model->all();

// Obtiene solo las columnas especificadas
$data = $model->all(['id', 'name']);
```

#### Filtrar resultados con `where`

Puedes encadenar cláusulas `where` para construir consultas más complejas.

```php
// Búsqueda simple: WHERE name = 'John'
$data = $model->where('name', 'John')->all();

// Usando otros operadores: WHERE name LIKE 'John%'
$data = $model->where('name', 'LIKE', 'John%')->all();

// Encadenando cláusulas: WHERE status = 'active' AND age > 30
$data = $model->where('status', 'active')->where('age', '>', 30)->all();

// Usando `orWhere`: WHERE status = 'active' OR is_premium = true
$data = $model->where('status', 'active')->orWhere('is_premium', true)->all();
```

#### Obtener el primer registro

Recupera el primer registro que coincida con la consulta.

```php
$data = $model->where('name', 'John')->first();
```

#### Comprobar valores nulos

```php
// WHERE deleted_at IS NULL
$data = $model->whereNull('deleted_at')->all();

// WHERE processed_at IS NOT NULL
$data = $model->whereNotNull('processed_at')->all();
```

#### Limitar la cantidad de registros

```php
// Obtiene los primeros 10 registros
$data = $model->limit(10)->all();

// Obtiene 10 registros, omitiendo los primeros 20 (paginación)
$data = $model->limit(10, 20)->all();
```

#### Contar registros

```php
// Cuenta todos los registros en la tabla
$total = $model->count();

// Cuenta los registros que cumplen una condición
$activeUsers = $model->where('status', 'active')->count();
```

### Depurar con `toSql`

Puedes ver la consulta SQL que se generará antes de ejecutarla.

```php
$sql = $model->where('name', 'LIKE', 'John%')->toSql();
// SELECT * FROM "users" WHERE "name" LIKE ?
```

## Creación de Registros

Creación simple usando un array.

```php
$data = $model->create([
    'name' => 'John',
    'email' => 'john@example.com',
]);
```

Creación usando una `Entity`. La ventaja es que puedes aprovechar los `casts` definidos en tu entidad.

```php
$data = $model->create(new Entity([
    'name' => 'John',
    'email' => 'john@example.com',
]));
```

## Actualización de Registros

Para actualizar registros, primero debes especificar qué filas modificar usando una cláusula `where`.

```php
$model->where('id', 1)->update([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
]);
```

## Eliminación de Registros

Por seguridad, no se permiten eliminaciones masivas por defecto. Siempre debes especificar una condición `where`.

```php
// Elimina el usuario con id = 1
$model->where('id', 1)->delete();
```
Si el modelo tiene `soft deletes` habilitado, este método actualizará el campo `deleted_at` en lugar de eliminar el registro permanentemente.
