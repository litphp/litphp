---
id: nimo
title: Introduction
sidebar_label: Introduction
---

Nimo helps you to organize your middlewares (and handlers).

## Installation

```sh
composer require litphp/nimo
```

## MiddlewarePipe

Pipe middlewares one after another, is the essential part of middleware pattern. And it's the core infrastructure of nimo.

```php
$pipe = new MiddlewarePipe();

$pipe->append($middleware); // append $middleware to last of the pipeline

$pipe->prepend($middleware2); // also you can do prepend
```

Thanks to our `MiddlewareTrait`, you can invoke `prepend` and `append` on any middleware instance provided by us. (or yours, just use the trait)

```php
$first = new NoopMiddleware();

$first->append($second); // same as (new MiddlewarePipe())->append($first)->append($second)
```

## Dummys

There are some basic middleware/handlers with straightforward behavior.

- `NoopMiddleware`: middleware doing nothing but delegate to the handler
- `FixedResponseMiddleware`: never call handler, just return that fixed response
- `FixedResponseHandler`: just return that fixed response
- `CallableHandler`: wraps a callable with signature `(RequestInterface): ResponseInterface` 
- `MiddlewareIncludedHandler`: include a middleware into a handler (`HandlerTrait::includeMiddleware` helps you do this directly on handler instance)
