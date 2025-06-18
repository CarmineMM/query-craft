# Debug

Puedes activar el debug para ver las consultas ejecutadas, el tiempo que tardan y la memoria usada del sistema.
También puedes tener este debug a la hora de ejecutar el ETL.

```php
\CarmineMM\QueryCraft\Facades\Debug::debugMode();
\CarmineMM\QueryCraft\Facades\Debug::debugMode(false); // Deactivate the debug
```

## Check the queries made

Obtains the list of consultations made, the time it took and the used memory.

```php
\CarmineMM\QueryCraft\Facades\Debug::getQueries();
```
