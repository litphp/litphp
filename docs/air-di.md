---
id: air-di
title: Dependency injection
sidebar_label: Dependency Injection
---

[Dependency injection](https://en.wikipedia.org/wiki/Dependency_injection) is a technique to fulfill [the IoC principle](https://en.wikipedia.org/wiki/Inversion_of_control) (**I**nversion **o**f **C**ontrol), or "Hollywood Principle", focusing on provide (inject) dependency for some object (product).

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

#### setter injection

Setter injection is an optional feature of air. When you call instantiate or produce, before the factory return the product, it will check for if there are registered injectors and try to inject dependencies. By default, all instance method begin with "inject" and requires exactly one parameter will be considered an injection point, the factory will run DI procedure to create the dependency and call that method.

There are some boilerplate works need to be done to make setter injection works. First you need include `SetterInjector::configuration()` in your container configuration (or you need more injectors, read its code!), then in class need setter injection, declare `const SETTER_INJECTOR = SetterInjector::class; `to tell setter injector to scan this class.

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

(see `Factory::resolveDependency`)

When air resolve a dependency, it inspect (with reflect API) to get below info first

+ string $basename: name of the dependent object
  + It's the classname of object which is being created (or injected on)
  + For `$factory->invoke` call, it's "!" following the callable name, full namespaced. For method, name of class and method is glued by `::`
+ string[] $keys "candicate key" of the dependency
  + for now all the dependency item are represent by parameter
  + the $keys are (ordered)
    1. name of the parameter
    2. classname if the parameter is type hinted
    3. the index of the parameter (not applicapable for setter injection)
+ string $classname: the classname if the parameter is type hinted 
+ array $extra: extra dependency entries in addition to container

Then `Factory` tries following ways of creating dependency

1. try to find corresponding **configuration** and resolve it
   + For each candicate key in `$keys`, look for `$extra` provided
   + Look for container entry with key `$basename . "::"` 
     + if `$basename` is a classname, also try look for it's parent class until no parent class is available

   > See [configuration](air-config#structure-of-configuration) section for more details about how a configuration value is resolved

2. if `$classname` is available, try to find container entry with key `$classname`
3. try to instantiate `$classname` directly

If all the above approach failed or not available, `Factory` would throw a `ContainerException`

If the procedure is `instantiate` or `produce`, after the instance is created, it will be scanned by registered injectors, and inject if any of them hits. For now the only bundled injector is `SetterInjector`

Since configuration / recipe resolving might involve cascading DI procudure when (just before) constructing instance, it's possible to happen circular dependency, which will be cauth by `Factory` and throw a specific `CircularDependencyException`. We recommend to rethink of your dependency of abstraction of code, try refactor your code to avoid this, but if that's not easy, you can use setter injection, which actually inject **after** object is created, thus can break the loop.

We don't talk about configuration here since it's a *circular dependency of documentation*! Configuration is resolved to recipe object, and recipe can call `Factory` to inject dependency (we do list them in this article), and when DI procedure is running, both configuration and recipe (registered to container) may be involved!

Now you should have enough idea about how DI in air works, please head to [configuration section](air-config), if you haven't read that yet.

