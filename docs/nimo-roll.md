---
id: nimo-roll
title: Write your own
sidebar_label: Write your own
---

Nimo features many traits / base classes for writing middlewares.

## Base classes

`AbstractHandler` and `AbstractMiddleware` are main base class of Nimo.

They implement `handle`/`process` method by:

1. first remember all the params in instance property
2. call the abstract `main` method

so that all the preceding logic wouldn't need to pass `$request` / `$handler` everywhere. For middleware, a `delegate` method is available to call `$this->handler` with `$this->request`

In addition, they used all traits provided by Nimo so all the below features are available.

## AttachToRequestTrait

Some middleware may carry useful runtime data, or function as a service to be referenced in later logic. In PSR standard, `RequestInterface` contains attribute mechanism to do this.

In Nimo, the `AttachToRequestTrait` provides ability that middleware can "attach" itself to request (attribute), so other middleware/handler can reference the instance easily.

```php
//in FooMiddleware A's main logic
$this->attachToRequest(); // to $this->request by default, also would return the attached request instance

//later, in other place
FooMiddleware::fromRequest($request) // retrieves the FooMiddleware instance early attached
```

The convention is to use class name as the attribute key, so in most case, name collision is auto avoided. This can be override by define `ATTR_KEY` const in class.

```php
class FooMiddleware extends AbstractMiddleware {
    const ATTR_KEY = 'foo';
}


//after attached, in other place
$request->getAttribute('foo') // the FooMiddleware instance
```

## Wrapper methods

These methods have been introduced in before sections, so just a quick list here.


_For handlers_
```php
$handler->includeMiddleware($middleware);
$handler->catch($catcher, $catchClassName);
```
_For middlewares_
```php
// piping
$middleware->append($another)
$middleware->prepend($another)
// conditional run
$middleware->when($prediction);
$middleware->unless($prediction);
// error handling
$middleware->catch($catcher, $catchClassName);
```
