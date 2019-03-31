---
id: air-local
title: Local entries
sidebar_label: Local Entries
---

You can store value such as object or arbitrary value in air container directly (local entry). Simply invoke `$container->set`

```php
assert(!$container->has('answer'));
$container->set('answer', 42);
assert($container->has('answer'));
assert($container->get('answer') === 42);
```

Some other related methods are `flush` to remove previously set entry, and `hasLocalEntry` to detect if there's a local entry. (where `has` take recipe or delegated container into account)

