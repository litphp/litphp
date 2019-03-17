---
id: air
title: Introduction
sidebar_label: Introduction
---

Air is the dependency injection factory for litphp

## Installation

```sh
composer require litphp/air
```

## Concept of DI and air

You may read [this page from PHP-DI](http://php-di.org/doc/understanding-di.html) for basic concept of dependency injection.

The two main class of air is `Factory` and `Container`, former is the DI factory, and the latter is container serving for DI factory. We devide them to two separate class, so `Container` can focus on container logic itself, a.k.a. highly customizable key-value storage, while `Factory` focus on the complex and tricky part in DI: autowiring.

Dynamic entries in `Container` need to implement `RecipeInterface` (and called **Recipe**), and recipe can be [decorated](https://en.wikipedia.org/wiki/Decorator_pattern) by **RecipeDocarator**s. This is also how `Container` call `Factory`  to create DI products: built-in recipes like `AutowireRecipe`  or `InstanceRecipe` calling `Factory` to produce.

## A little taste

```php
$container = new Container();
$container->set('foo', 'bar'); // write entry directly
assert($container->get('foo') === 'bar');

$container->define('baz', new AliasRecipe('foo')); 
// or ->define('baz', Container::alias('foo'))

assert($container->get('foo') === 'bar'); // use alias recipe to create alias for entry

class A {}
class B {
  public $a;
  public function __construct(A $a) {
    $this->a = $a;
  }
}

$b = Factory::of($container)->produce(B::class);
assert($b instanceof B);
assert($b->a instanceof A);
```

