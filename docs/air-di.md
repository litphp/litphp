---
id: air-di
title: Dependency injection
sidebar_label: Dependency Injection
---

[Dependency injection](https://en.wikipedia.org/wiki/Dependency_injection) is a technique to fulfill [the IoC principle](https://en.wikipedia.org/wiki/Inversion_of_control) (**I**nversion **o**f **C**ontrol), or "Hollywood Principle", focusing on provide (inject) dependency for some object (product).

+ Factory
  + instantiate & produce
  + dependency resolve rule
  + invoke
  + setter injection and more injector
+ Autowire & instance recipe, config

We devide this article into two parts. First we'll look into the ways to call DI procedure, and then we talk about how to provide (and control) dependencies.

## Calling dependency injection

#### get $factory instance

First of all you should get your `Factory` instance where all DI functionality resides on. Our `Factory` depends on a `Container` to store dependencies & configurations, but the constructor just typehint on PSR `ContainerInterface` and allow null value.

+ if `Container` instance is provided, `Factory` use that directly
+ if arbitrary PSR `ContainerInterface` instance is provided, `Factory` create a `Container` and use `$container->setDelegateContainer` so missing entries will be fallback to provided container instance
+ if null is provide, `Factory` create an empty `Container` itself

After initialted, Factory will set itself into corresponding `$container` instance, so if you have container instance, please use `Factory::of($container)` to get factory instance.

#### instantiate & produce

The most straightforward way to call DI procedure is `$factory->instantiate`, it take \$classname as first parameter, optionally followed by extra parameter (array of dependency), and return a instance of  \$classname

Every time you call `$factory->instantiate` the factory will create a new instance for you, but chances are that you want to create instance only once and reuse it on preceding calls, that's where `$factory->produce` should be used. It takes exactly same parameter with instantiate, behind the scenes we just use the classname to identify instance, so make sure you pass same `$extra` everytime. Only first time the object is created and that `$extra` is used, later calls of same \$classname will simple get previously created instance, the `$extra` will be silently ignored.

#### invoke

Instead of creating objects, `$factory->invoke` calls a callable when use dependency injection mechanism to resolve parameters. Like instantiate & produce, it receive optional second parameter `$extra` to provide dependencies in addition to the container

#### recipes related to DI

Finally, there're some recipes calls factory to start DI procedure

| Recipe         | Factory method | Example                                                      |
| -------------- | -------------- | ------------------------------------------------------------ |
| AutowireRecipe | produce        | when `$classname` is null, the recipe will use container key as classname |
| InstanceRecipe | instantiate    | `$classname` can also be null                                |
| BuilderRecipe  | invoke         | can be used to build non-object value, or call 3rd-party factory method |

## Working on dependencies

