---
id: air-config
title: Configuration
sidebar_label: Configuration
---

Although configuration may well be most used part in air, we put it at last part, since it's basically related to all features of air. In short words, configuration is a way to describe state of container in native PHP array. Being a native PHP array, we keep it easy to do both side: you can easily call `json_decode` or `Yaml::parse` or any other parsing method to get it from some format, AND you can also build it up with PHP, with help of type hinting and IDE support. We provide a `Configurator` class, which includes a bunch of static method, to build the configuration array.

## Structure of configuration

In configuration array, the top-level key is the entry key of container, and the value is parsed as

+ If value is object implementing  `Recipeinterface` (recipe object), it's used directly (register to container)
+ If value is a callable, it's recognized as a builder function, will return a singleton-ized (decorated) `BuilderRecipe` object with value. If you've used [Pimple](https://github.com/silexphp/Pimple) you will find way home
+ If value is an array with key '$', it's preserved "array notation" of recipe, will be converted to recipe object (more detail in next section)
+ Finally, we use the value directly, that's said scalar type, resource type, most user object can be directly used.
  + for array, we recommend always wrap with `Container::value` (FixedValueRecipe) to be explicit

Keys ended with `::` should be array providing dependency for [DI procedure](air-di), it's value must be array, and items inside are parsed by above rule

## Array notation of recipe object

Here's an example

```php
[
  '$' => 'instance', // type
  'decorator' => [
    'decorator_name' => $decorator_option,
    // ...
  ],
  
  // integer keys: parameters of Container::instance() method
  $className,
  $extra,
]
```

> PHP array can contain integer and string keys at same time, and unkeyed entry can be mixed with keyed entry without problem. Under the stage we just unset `$` and `decorator` from the array and pass remained values to Container::xxx method

The possible $ types are listed below, they are just static method name of `Container` which return a recipe object.

| $        | Recipe           | Method Signature                                         |
| -------- | ---------------- | -------------------------------------------------------- |
| alias    | AliasRecipe      | `alias(string $alias)`                                   |
| autowire | AutowireRecipe   | `autowire(?string $className = null, array $extra = [])` |
| instance | InstanceRecipe   | `instance(?string $className = null, array $extra = [])` |
| builder  | BuilderRecipe    | `builder(callable $builder, array $extra = [])`          |
| value    | FixedValueRecipe | `value($value)`                                          |

For decorator, the name should be `singleton` or `callback` for bundled decorators, and the decorator clasname if you write your custom one, the value are setted as decorator option. The singleton decorator don't need option so just use `null` , and the callback decoration's option is the callback (wrapper).

## Configuration conventions

#### single C

`use Lit\Air\Configurator as C;` this makes it easier to read and write configuration files.

#### naming and namespacing

Class names and interface names should be used to provide default value or default implementation.E.g. you may set psr `LoggerInterface` to provide a logger for most of your objects, but also can specify for some class (maybe important ones) to use some other logger instance.

Configuration keys should namespaced by delimeter `::`, `C::join` method concat parameter strings for you. 

#### alias

#### merging configuration

