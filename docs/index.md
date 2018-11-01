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
+ We enable [setter injection](#setter-injection) in our `BoltAbstractAction`. In fact, this is the only recommended use case of setter injection.
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

**Router** is where we match the request and find correct **action**.

#### Routing to find stub

We use `FastRouteConfiguration` from `litphp/router-fast-route` package, which uses `nikic/fast-route` to do actual routing logic. The `$routes` param fed into it is a callback with `\FastRoute\RouteCollector` param, on which you can invoke methods to add route into FastRoute. (If you want write a class for it, you can extend `FastRouteDefinition`, which is a invokable class with same signature)

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

We use `null` value to indicate not found, and both `FastRouteRouter` and `BoltStubResolver` receives a `$notFound` parameter, to be used as the stub of not found. If finally the stub is still a `null`, or failed to be recognized, a `StubResolveException` will be thrown, which in the case, contains a default 404 response. Use a catch to catch that exception is another way to handle not found (by this way, it will include both router not found and wrong stub provided by router).

### Middleware

> Organize reusable logic with middleware

#### Create your middleware

Any [PSR-15](https://www.php-fig.org/psr/psr-15/) middleware can be used directly. If you are writing new ones, you may refer [this page](nimo-roll.md).

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

#### Embedded middleware

We provide some middleware useful to communicate between different part of your application by default.

##### RequestContext

This is a spl `\ArrayObject` subclass, implementing `MiddlewareInterface`, and attached to the request. Use this object saves keyspace of psr request attributes, and, methods on `\ArrayObject` is available.

```php
// in somewhere you may write the context
RequestContext::fromRequest($request)['foo'] = 'bar';


// later you may read from it
$foo = RequestContext::fromRequest($request)['foo']; // 'bar'
// we enable ARRAY_AS_PROPS flag by default, so this also works
$foo = $this->context()->foo; // 'bar'
// p.s. if you are in `BoltAbstractAction`, just use $this->context() to get RequestContext instance like above

```

##### EventsHub

This is `symfony/event-dispatcher` integration. You can find a `EventDispatcher` instance inside this.

```php
// we passthru `addListener` and `dispatch` method to EventDispatcher
EventsHub::fromRequest($request)->addListener(MyEvent::EVENT_FOO, $listener);

// events() method in `BoltAbstractAction` get the instance
// later you may trigger above listener
$this->events()->dispatch(MyEvent::EVENT_FOO, new MyEvent());

// if you need other method of EventDispatcher, get it
$this->events()->getEventDispatcher();// the instance
```

Also, EventsHub will trigger before & after event around inner logic

```php
BoltEvent::EVENT_BEFORE_LOGIC
BoltEvent::EVENT_AFTER_LOGIC
```

Note although the instance is attached to request, but the before event is dispatched **before** action runs, so you need listen to it early. Get the event hub instance by `$app->getEventsHub()` in that case.

The `BoltEvent` is sent with these two event. You may change the `$beforeEvent['request']` or `$afterEvent['response']` to change the request / response. You may set `$beforeEvent['response']` (by default it's not exist) to intercept the logic. `$afterEvent['request']` is provided, but changing that has no effect.

### Dependency Injection

> Connect everything with dependency injection

We assume you have basic concept of dependency injection here. If you don't, you may read some [introduction from php-di](http://php-di.org/doc/understanding-di.html) first.

In the example above, we run our app like this

```php
BoltZendRunner::run(FastRouteConfiguration::default($routes));
```

As our convention, class named with `Configuration` provides DI config (a php array) to our DI factory, indicating how to instantiate other class. `Lit\Air\Configurator` is the helper class to build such config array, we usually alias it to make configuration more readable `use Lit\Air\Configurator as C;`

#### Basic Configuration

##### Instance and singleton

Create a `FastRouteRouter` singleton instance for who need a `RouterInterface`

```php
RouterInterface::class => C::singleton(FastRouteRouter::class),
```

If you need multiple instance, use `C::instance` instead. There's a second parameter `array $extra`, we will discuss it later.

##### Builder

Write a builder method / closure to create you instance.

```php
Schema::class => function (SourceBuilder $sourceBuilder, TypeConfigDecorator $typeConfigDecorator) {
    return BuildSchema::build($sourceBuilder->build(), $typeConfigDecorator);
},
```

(the example comes from experimental GraphQL integration)

We use a closure directly here, that means a singleton builder. If you need multiple instance, use `C::builder` instead.

Also you can see the builder can have some parameter, which will be injected automatically.

##### Value / Alias

Use `C::value` to wrap a pre-populated value, we recommend you always do this. Currently, closure and array containing key `$` are required to be wrapped, but this may change in future.

Use `C::alias` to get some other value from the DI container. This can be useful for embedded configuration ($extra). Note that when you use some class name as alias, that class will not be autowired by default. You should add a `YOURCLASS => C::produce()` entry in configuration to indicate that.

There is a example use of alias in next section.

##### Autowire and extra parameters

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

Here's the default configuration we provide for `\FastRoute\Dispatcher` (interface)

```php
//configurataion entries
Dispatcher::class => C::singleton(
    CachedDispatcher::class,
    [// we skip some of the param, also there are callable and string param, so we use param name as key here
        'cache' => C::alias(Dispatcher::class, 'cache'), //alias so can be easily overrided
        'routeDefinition' => C::alias(FastRouteDefinition::class),  //alias so can be easily overrided
        'dispatcherClass' => Dispatcher\GroupCountBased::class,
    ]
),
DataGenerator::class => C::singleton(DataGenerator\GroupCountBased::class),
RouteParser::class => C::singleton(RouteParser\Std::class),
C::join(Dispatcher::class, 'cache') => C::produce(VoidSingleValue::class), // provide default implementation

//CachedDispatcher's constructor signature
function __construct(
    SingleValueInterface $cache, //provided in $extra
    RouteParser $routeParser,// not in $extra, provided by class name key
    DataGenerator $dataGenerator,// same as above
    callable $routeDefinition,// provided in $extra
    string $dispatcherClass// provided in $extra
)
```

As you can see, the configuration array value can be "embedded" via `$extra.` But we use a simple array union (`+`) to merge configurations, it works well for the top level keys, but no luck in deep ones. We recommend use `alias` to solve this problem, so the configuration merge is still a single `+`. You should namespace the alias key with `C::join` which is a simple string concat helper with our recommended delimeter `::`

#### Setter Injection

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

        return $this; // not required
    }

```

