---
id: concepts
title: Core concepts of Lit
sidebar_label: Core concepts
---

## Action

> Write your controller code in action

**Action** is the **C** part in MVC pattern. We take the name action because in many other framework, controller is a class containing multiple endpoint (RequestHandler), whereas action is only one. `BoltAbstractAction` is the dafault base class of action, but we recommend you to have your own base class, even without any code, that extends `BoltAbstractAction`

+ Action class is one implementation of `RequestHandlerInterface`, any other implementation can also be used.
+ We enable [setter injection](#setter-injection) in our `BoltAbstractAction`. In fact, this is the only recommended use case of setter injection.
+ Psr `ResponseFactoryInterface` is required and by default provided by `BoltZendConfiguration`
+ Action could create view to render response. By default the `json()` method creates a view that render json output. More (template engine view, view with your restful convention, etc) can be added in your base action.

## View

> Populate response in view

**View**, by our definition, is a helper class which is responsible to generate response object for action class. It's more close to **Responder** in [ADR pattern](http://pmjones.io/adr/).

### Integrating template engine

There's a working [twig integration](https://github.com/litphp/view-twig). If you want to work with other template engine, you may also look at it to see how to integrate a template engine.

## Router

> Locate and populate action class in router

**Router** is where we match the request and find correct **action**.

### Routing to find stub

We use `FastRouteConfiguration` from `litphp/router-fast-route` package, which uses `nikic/fast-route` to do actual routing logic. The `$routes` param fed into it is a callback with `\FastRoute\RouteCollector` param, on which you can invoke methods to add route into FastRoute. (If you want write a class for it, you can extend `FastRouteDefinition`, which is a invokable class with same signature)

The value you fed into `RouteCollector` is called **stub**, it's a stub for concrete **action** class, so you can delay the instantiate, reducing cost for having bunch of actions (often with different but many service instances bounded)

### Resolving the stub

**Stub** can be following value

+ A `callable` value which follow the signature of `RequestHandlerInterface::handle` (request => response)
+ A `RequestHandlerInterface` instance, it's used directly
+ A `ResponseInterface` instance, it's returned without any other operation
+ At last, a string class name, or array with two value `[string $classname, array $extraParameters]`, this is delegated to `Facotry::instantiate`. see dependency injection part for more details

You can change this behaviour by implementing `RouterStubResolverInterface` and fed it into **router** instance.

### Getting route arguments

The variable caught by `FastRoute` is available in `RouteArgumentBag::fromRequest($request)->get(KEY)`, or use `RouteArgumentBagGetterTrait` and `$action->routeArgs()->get(KEY)`.

If you want access route arguments in psr request attributes, use `\Lit\Router\FastRoute\ArgumentHandler\ArgumentToAttribute` instead of default `\Lit\Router\FastRoute\ArgumentHandler\RouteArgumentBag`

### Not found

We use `null` value to indicate not found, and both `FastRouteRouter` and `BoltStubResolver` receives a `$notFound` parameter, to be used as the stub of not found. If finally the stub is still a `null`, or failed to be recognized, a `StubResolveException` will be thrown, which in the case, contains a default 404 response. Use a catch to catch that exception is another way to handle not found (by this way, it will include both router not found and wrong stub provided by router).

## Middleware

> Organize reusable logic with middleware

### Create your middleware

Any [PSR-15](https://www.php-fig.org/psr/psr-15/) middleware can be used directly. If you are writing new ones, you may refer [this page](nimo-roll).

### Attach middleware to your app

The `MiddlewareInterface` fed into `BoltApp` is the default middlware injection point. You can pass any middleware instance into it. Or if you have multiple middleware, assemble them into one with `MiddlewarePipe`

```php
// configuration
C::join(BoltApp::class, MiddlewareInterface::class) => function() {
    $middlewarePipe = new MiddlewarePipe();

    $middlewarePipe->append(SOME_MIDDLEWARE);
    $middlewarePipe->append(ANOTHER_MIDDLEWARE);

    return $middlewarePipe;
},
```

## Dependency Injection

> Connect everything with dependency injection

We assume you have basic concept of dependency injection here. If you don't, you may read some [introduction from php-di](http://php-di.org/doc/understanding-di.html) first.

In our [template project](https://github.com/litphp/project), we run our app like this

```php
BoltZendRunner::run($configuration); // configuration for `litphp/air` (dependency injection)
```

### Basic Configuration

#### Instance and singleton

Create a `FastRouteRouter` singleton instance for who need a `RouterInterface`

```php
RouterInterface::class => C::singleton(FastRouteRouter::class), // use Lit\Air\Configurator as C;
```

If you need multiple instance, use `C::instance` instead. There's a second parameter `array $extra`, we will discuss it later.

#### Builder

Write a builder method / closure to create you instance.

```php
Schema::class => function (SourceBuilder $sourceBuilder, TypeConfigDecorator $typeConfigDecorator) {
    return BuildSchema::build($sourceBuilder->build(), $typeConfigDecorator);
},
```

(the example comes from experimental GraphQL integration)

We use a closure directly here, that means a singleton builder. If you need multiple instance, use `C::builder` instead.

Also you can see the builder can have some parameter, which will be injected automatically.

#### Value / Alias

Use `C::value` to wrap a pre-populated value, we recommend you always do this. Currently, closure and array containing key `$` are required to be wrapped, but this may change in future.

Use `C::alias` to get some other value from the DI container. This can be useful for embedded configuration ($extra). 

#### Autowire and extra parameters

DI factory will try to populate parameters of constructor and builder function required. The precedence is:

1. Extra
    + search the `$extra` parameter provided in `C::instance`/`C::singleton` first, the key `"$classname::"` in container secondly.
    + for each array provided, search following key
        1. the parameter name
        2. the parameter classname (typehint)
        3. the parameter index (?th parameter, zero-based)
    + the value can be another configuration array value
2. Defined configuration entry with parameter classname (typehinted) as key
3. Try to populate instance by classname
4. Default value of the parameter
5. At last, throw a ContainerException

### Setter Injection

Constructor injection works in most cases, but **action** classes are often coupled with many other classes, constantly changing these dependencies, and may often be extended serveral times (making write all dependency in constructor function clumsy). We provide setter injection for this use case. Of course you can enable it in any other class, by adding a const `const SETTER_INJECTOR = SetterInjector::class;`

We scan all the public method start with `inject` with one required parameter, then re-use the [autowire](#autowire-and-extra-parameters) process to populate dependency, and invoke the inject method. Below is how we inject `ResponseFactoryInterface` to our base action class.

```php
//in action class
/**
 * @var ResponseFactoryInterface
 */
protected $responseFactory;
public function injectResponseFactory(ResponseFactoryInterface $responseFactory)
{
    $this->responseFactory = $responseFactory;
}

```

#### More details

There are more details about dependency injection at [guide about lit/air](air).

## Runners

You should continue to [runners](runner) section to see options about how to actually run your 
