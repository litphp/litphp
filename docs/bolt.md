---
id: bolt
title: Introduction
sidebar_label: Introduction
---

Bolt is the microframework of litphp, it's a thin wrapper over `litphp/air` + `litphp/voltage`, aims to be a good start point of any project.

## Installation

```sh
composer require litphp/bolt
```

## Quickstart

You can use `litphp/project` boilerplate to create a standard bolt project quickly

```sh
composer create-project --remove-vcs -s dev litphp/project myproject
```

## Air (DI) on Voltage

+ The two extension to voltage `BoltApp` and `BoltAbstractAction`  enabled setter injection
+ `BoltContainerConfiguration` should be included in your air configuration (although it only contain configuration about setter injection for now), and `RouterConfiguration` as it's name, is useful when you need router logic in your app
+ a `BoltStubResolver` is provided which empowers route stub with DI, it resolve following `$stub` values
  + at first null value will fallback to notFound (provided in constructor)
  + callable value will be wrapped by `\Lit\Nimo\Handlers\CallableHandler` so a simple Request => Response callable works as expected
  + RequestHandler instance will be use directly
  + Response instance will be returned without any operation (`\Lit\Nimo\Handlers\FixedResponseHandler`)
  + At last, a "container stub", can be a string class name, or array in form of `[$className, $params]`, will be passed to $container->instantiate, and it should return a RequestHandler instance

## Built-in middleware

We include two middleware which can help you communicate between different part of your application and organize your application logic better

### RequestContext

This is a spl `\ArrayObject` subclass, implementing `MiddlewareInterface`, and attached to the request. Use this object saves keyspace of psr request attributes, and, methods on `\ArrayObject` are all available.

```php
// in somewhere you may write the context
RequestContext::fromRequest($request)['foo'] = 'bar';


// later you may read from it
$foo = RequestContext::fromRequest($request)['foo']; // 'bar'
// we enable ARRAY_AS_PROPS flag by default, so this also works
$foo = $this->context()->foo; // 'bar'
// p.s. if you are inside `BoltAbstractAction`, just use $this->context() to get RequestContext instance like above

```


### EventsHub

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

Note although the instance is attached to request, the `EVENT_BEFORE_LOGIC` event is dispatched **before** action runs, so you need listen to it early. Get the event hub instance by `$app->getEventsHub()` in that case.

The `BoltEvent` is sent with these two event. You may change the `$beforeEvent['request']` or `$afterEvent['response']` to change the request / response. You may set `$beforeEvent['response']` (by default it's not exist) to intercept the logic. `$afterEvent['request']` is provided, but changing that has no effect.
