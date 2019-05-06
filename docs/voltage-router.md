---
id: voltage-router
title: Router
sidebar_label: Router
---

In this article we basicly talking about design concepts of router. For a quick glance of how to use, check our [quickstart](quickstart) guide.


Routing is about answering "What should I do". There are two core concept in our routing machanism: **router** and **dispatcher**.

`RouterDispatchHandler`, act as **dispatcher**, implements `RequestHandlerInterface`, call it's router to locate real business RequestHandler, and delegate request to it.

On the other side, **router** is a interface with a simple method, contains all routing logic.

```php
function route(ServerRequestInterface $request): RequestHandlerInterface
```

The way you use a router looks like something like

```php
$router = new MyCoolRouter();
//somehow register all the route entries

$app = new App(new RouterDispatchHandler($router));

emit_response($app->handle($request));
```

In our `AbstractRouter`, we introduce another concept called **stub**. **Stub** breaks routing process into two phase, in the routing phase you just analyze the request and find the correct **stub**. In second phase, you resolve the **stub** to get the real `RequestHandlerInterface` instance. The point here is, stub can be any type and should be a cheap one (one simple string, or some array of strings), while request handlers is likely to be the most complicated part in your application. It's very common that a handler instance will carry many other service instance. At worst they may carry external resources, like DB connections or things like that, at least the class / bytecode / autoload process do hurt performance.

There are two things need to be implemented to utilize `AbstractRouter`: For the first phase, you need to implement `AbstractRouter::findStub` to parse the request and find the corresponding **stub**, you may call   your 3rd party routing component here, or implement your own routing logic; For the second phase, you need to implement a `RouterStubResolverInterface` with the `resolve` method, to convert your **stub** into some real request handler. If you somehow don't need the stub resolve mechanism, you may just use `\Lit\Voltage\RouteStubResolver` which is a noop (just return the **$stub** itself). In this case, you need generate the request handler instance directly in `findStub` and return the hander. 

Spread the implementation in two class make it possible to reuse logic. You can change your router implementation, or even use two different router for dirrerent request (remember `RouterDispatchHandler` is also a handler, you may somehow let your router A to return a `RouterDispatchHandler` which contains another router B), keeping stub resolving untouched. Or change/add new stub resolving mechanism (e.g. implement old "controller" style routing, instantiate controller and wrap the controller + method in a handler instance inside your StubResolver)

A `\Lit\Voltage\BasicRouter` implementation exists which just do routing by array with path/method as offset. See [src/Lit/Core/_example/routing.php](https://github.com/litphp/litphp/blob/master/src/Lit/Core/_example/routing.php) for a working example

`\Lit\Bolt\Router\BoltStubResolver` in bolt package is a more realistic stub resolver that use dependency inject to instantiate your handler. /* add link here */

`litphp/router-fast-route` package is there for `nikic/fast-route` integration.
