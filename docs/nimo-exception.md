---
id: nimo-exception
title: Handling Exceptions
sidebar_label: Exception handling
---

Both handler & middleware in Nimo provides a `catch` wrapper method to catch exception

```php
$handler->catch($catcher, \Throwable::class);
$middleware->catch($catcher, \RuntimeException::class);
```

where `$catcher` is instance of `ExceptionHandlerInterface`

```php
handle(
  \Throwable $exception,
  ServerRequestInterface $request,
  RequestHandlerInterface $orignalHandler,
  MiddlewareInterface $originalMiddleware
): ResponseInterface
```

This is powered by `CatchMiddleware` and `MiddlewareTrait`/`HandlerTrait`. To use this without using trait on 3rd party middlewares, 
instantiate the `CatchMiddleware` directly

```php
$wrapped = new CatchMiddleware($middleware, $catcher, $catchClassName);
```

for handlers, use `NoopMiddleware` & `MiddlewareIncluedHandler` to wrap

