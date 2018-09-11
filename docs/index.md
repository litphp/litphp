---
id: index
title: Documentation
sidebar_label: Introduction
---

## QuickStart

We start from the example 

```php
class MyAction extends BoltAbstractAction
{
    protected function main(): ResponseInterface
    {
        return $this->json()->render([
            'test.json' => 'should be this',
            'bool' => false,
            'nil' => null,
            'query' => $this->request->getQueryParams(),
        ]);
    }
}

$routes = function (RouteCollector $routeCollector) {
    $routeCollector->get('/test.json', MyAction::class);
};

BoltZendRunner::run(FastRouteConfiguration::default($routes));
```

### Action

> Write your controller code in action

**Action** is the **C** part in MVC pattern. We take the name action because in many other framework, controller is a class containing multiple endpoint (RequestHandler), whereas action is only one. `BoltAbstractAction` is the dafault base class of action, but we recommend you to have your own base class, even without any code, that extends `BoltAbstractAction`

+ Action class is one implementation of `RequestHandlerInterface`, any other implementation can also be used.
+ We enable setter injection (link TODO) in our `BoltAbstractAction`. In fact, this is the only recommended use case of setter injection.
+ Psr `ResponseFactoryInterface` is required and by default provided by `BoltZendConfiguration`
+ Action could create view to render response. By default the `json()` method creates a view that render json output. More (template engine view, view with your restful convention, etc) can be added in your base action.

### View

> Populate response in view

**View**, by our definition, is a helper class which is responsible to generate response object for action class.

#### Integrating template engine

We are planning to provide some adapter view implementations for popular template engine, before that, you can find a simple example [here](lit-view.md)

If you are using some view other that `JsonView`, you should add a factory method for it in your base action class. 

```php
    // in action class
    protected function plate(string $name)
    {
        $view = $this->plateViewFactory->produce();
    
        return $this->attachView($view->setTemplateName($name));
    }
```

### Router

> Locate and populate action class in router

**Router** is how you match the request and find correct **action**.

#### Routing to find stub

We use `FastRouteConfiguration` from `litphp/router-fast-route` package, which is a `nikic/fast-route` adapter.The `$routes` param fed into it is a callback with `\FastRoute\RouteCollector` param, on which you can invoke methods to add route into FastRoute. (If you want write a class for it, you can extend `FastRouteDefinition`, which is a invokable class with same signature)

The value you fed into `RouteCollector` is called **stub**, it's a stub for concrete **action** class, so you can delay the instantiate, reducing cost for having bunch of actions (often with different but many service instances bounded)

#### Resolving the stub

**Stub** can be following value

+ A `callable` value which follow the signature of `RequestHandlerInterface::handle` (request => response)
+ A `RequestHandlerInterface` instance, it's used directly
+ A `ResponseInterface` instance, it's returned without any other operation
+ At last, a string class name, or array with two value `[string $classname, array $extraParameters]`, this is delegated to `Facotry::instantiate`. see dependency injection part for more details

You can change this behaviour by implementing `RouterStubResolverInterface` and fed it into **router** instance.

#### Getting route variables

The variable caught by `FastRoute` is available in `$request->getAttribute(KEY)`

#### Not found

Both `FastRouteRouter` and `BoltStubResolver` receives a `$notFound` parameter, to 

### Middleware

> Organize reusable logic with middleware

#### Attach middleware to your app

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

#### Write your own middleware

Nimo is 

### Dependency Injection

> Connect everything with dependency injection