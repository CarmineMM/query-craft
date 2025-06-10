# Entity digging deeper

Know how to create custom Casts, possible types of casts and how to better use the entity.

### Possible casts (Predefined)

There are predefined casts that you can use in the entity.Both when you bring new records and when you create or update records.

```php
protected array $casts = [
    'field' => 'json',
    'field' => 'object',
    'field' => 'datetime',
    'field' => 'array',
];
```
