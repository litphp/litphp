---
id: air-config
title: Configuration
sidebar_label: Configuration
---

Although configuration may well be most used part in air, we put it at last part, since it's basically related to all features of air. In short words, configuration is a way to describe state of container in native PHP array. Being a native PHP array, we keep it easy to do both side: you can easily call `json_decode` or `Yaml::parse` or any other parsing method to get it from some format, AND you can also build it up with PHP, with help of type hinting and IDE support. We provide a `Configurator` class, which includes a bunch of static method, to build the configuration array.

## Structure of configuration

In configuration array, the top-level key is the entry key of container, and the value is resolved by following rules

+ If value is object implementing  `Recipeinterface` (recipe object), it's used directly (register to container)
+ If value is a callable, it's recognized as a builder function, will return a singleton-ized (decorated) `BuilderRecipe` object with value. If you've used [Pimple](https://github.com/silexphp/Pimple) you will find way home
+ If value is an array with key '$', it's preserved "array notation" of recipe, will be converted to recipe object (more detail in next section)
+ Finally, we use the value directly, that's said scalar type, resource type, most user object can be directly used.
  + for array, we recommend always wrap with `Container::value` (FixedValueRecipe) to be explicit

Keys ended with `::` should be array providing dependency for [DI procedure](air-di#working-on-dependencies), it's value must be array, and items inside are parsed by above rule

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

> PHP array can contain integer and string keys at same time, and unkeyed entry can be mixed with keyed entry without problem. Under the hood we just unset `$` and `decorator` from the array and pass remained values to Container::xxx method

The possible $ types are listed below, they are just static method name of `Container` which return a recipe object.

| $        | Recipe           | Method Signature                                         |
| -------- | ---------------- | -------------------------------------------------------- |
| alias    | AliasRecipe      | `alias(string $alias)`                                   |
| autowire | AutowireRecipe   | `autowire(?string $className = null, array $extra = [])` |
| instance | InstanceRecipe   | `instance(?string $className = null, array $extra = [])` |
| builder  | BuilderRecipe    | `builder(callable $builder, array $extra = [])`          |
| value    | FixedValueRecipe | `value($value)`                                          |

For decorator, the name should be `singleton` or `callback` for bundled decorators, and the decorator classname if you write your custom one, the value are setted as decorator option. The singleton decorator don't need option so just use `null` , and the callback decorator's option is the callback (wrapper).

## Configuration conventions

#### single C

`use Lit\Air\Configurator as C;` this makes it easier to read and write configuration files.

#### naming and namespacing

Class names and interface names should be used to provide default value or default implementation.E.g. you may set psr `LoggerInterface` to provide a logger for most of your objects, but also can specify for some class (maybe important ones) to use some other logger instance.

Configuration keys should namespaced by delimeter `::`, `C::join` method concat parameter strings for you. 

#### alias

There're some `C::` method receiving `array $extra` parameter, which wil be fed into `$factory->instantiate` / `$factory->produce`. The value in `$extra` is again treated as configuration, so it's pretty common to nest configuration in configuration. We recommend use alias to flatten such nesting.

`C::alias` receives multiple (namespaced) strings and will call `C::join` for you.

#### merging configuration

When merging different part of configuration (or overriding some), we recommend to use native array union operator (`+` or `+=`). This is easy to write and manage, and should be enough if you follow our convention.

#### configuration class / method

If you're writing some reusable or default configuration, it's very common that to write a method to return a configuration array. Our convention arount this is use `FooBar::configuration(/*parameters*/)` for configuration method, and use `FooConfiguration` as configuration class, and `FooConfiguration::default(/*parameters*/)` as the default configuration method. 

When your configuration is closely related to a single class, just add a configuration method to it. When there are (or will be) multiple related class, or your class or configuration itself is complicated, or having many other static method, you may want a dedicated configuration class.

These configuration methods should be static, and may have parameters.

## A commented example

Here's the configuration method for FastRouteRouter which integrates `nikic/FastRoute`

```php
use FastRoute\DataGenerator\GroupCountBased as GCBDataGenerator;
use FastRoute\Dispatcher\GroupCountBased as GCBDispatcher;

    public static function default($routeDefinition)
    {
        if (is_callable($routeDefinition)) {
            // use C::value to wrap callable value so they can be passed directly
            $routeDefinition = C::value($routeDefinition);
        }

        return [
                // provide implementation for RouterInterface, required by RouterConfiguration
                // use alias to escape nesting
                RouterInterface::class => C::alias(FastRouteRouter::class), 

                // since the key name is just the classname, we use `C::provideParameter`, which fed `null` to `$classname` of `AutowireRecipe`, let it use key as classname
                FastRouteRouter::class => C::provideParameter([
                    // constructor parameters, again use alias to escape
                    Dispatcher::class => C::alias(Dispatcher::class),
                    RouterStubResolverInterface::class => C::singleton(BoltStubResolver::class), // it's rare to override this, so just hardcode here
                    'methodNotAllowed' => C::alias(FastRouteRouter::class, 'methodNotAllowed'),
                    'notFound' => C::alias(FastRouteRouter::class, 'notFound'),
                ]),
          		// null value of configuration will be resolved to null
                C::join(FastRouteRouter::class, 'methodNotAllowed') => null,
                C::join(FastRouteRouter::class, 'notFound') => null,
			    // FastRoute\Dispatcher is interface, so can't use `provideParameter` as above
                Dispatcher::class => C::singleton( // use C::produce here is also good, there differences are pretty subtle, but singleton is more safe to use
                    CachedDispatcher::class,
                    [
                        'cache' => C::alias(CachedDispatcher::class, 'cache'),
                        DataGenerator::class => C::alias(CachedDispatcher::class, DataGenerator::class),
                        RouteParser::class => C::alias(CachedDispatcher::class, RouteParser::class),
                        'routeDefinition' => C::alias(CachedDispatcher::class, 'routeDefinition'),
                        'dispatcherClass' => GCBDispatcher::class, // this is a plain string
                    ]
                ),
                // VoidSingleValue is a stateless class, without construct parameter and no reason to create a second instance, use `C::produce` under this situation to reuse it's instance
                C::join(CachedDispatcher::class, 'cache') => C::produce(VoidSingleValue::class),
                C::join(CachedDispatcher::class, DataGenerator::class) => C::singleton(GCBDataGenerator::class),
                C::join(CachedDispatcher::class, RouteParser::class) => C::singleton(StdRouteParser::class),
                C::join(CachedDispatcher::class, 'routeDefinition') => $routeDefinition,
            ] + RouterConfiguration::default(); // merge the configuration of common router apps
    }

```

That's really a lot of configurations! For comparison we'll write some pseudo code of creating such object without DI and without singleton (reuse), and hardcode everything.

> Note the order of configuration is naturally top-down since you write it by fixing dependency exceptions, but the hand write constructing code must be bottom-up so it's harder to read

```php
$parser = new StdRouteParser();
$dg = new GCBDataGenerator();
$cache = new VoidSingleValue();

$dispatcher= new CachedDispatcher($cache, $db, $parser, $routeDefinition, GCBDispatcher::class);

$stubResolver = new BoltStubResolver($container);
$stubMethodNotAllowed = null;
$stubNotFound = null;
$router = new FastRouteRouter($dispatcher, $stubResolver, $stubMethodNotAllowed, $stubNotFound);

// use $router as RouterInterface
```
