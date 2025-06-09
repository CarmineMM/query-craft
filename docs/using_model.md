# Using model

The model can and should be used to obtain data from the database.
In turn create, update and delete.

### Simple data retrieval

```php
// Use to obtain all columns
$data = $model->all();

// Use to obtain only certain columns
$data = $model->all(['id', 'name']);
```
