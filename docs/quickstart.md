---
id: quickstart
title: Quick Start
sidebar_label: Quick Start
---

To create a new project, you may use our project boilerplate:

```bash
composer create-project --remove-vcs -s dev litphp/project myproject
# you will be prompt to input your project namespace, we do the replace work for you
cd myproject
php -S 127.0.0.1:3080 public/index.php

```

Before write your business logic, there're some decision you need to take. For some of them we've already pick a default solution (often the most popular one).

## How to run the app

FPM with a single entry point `index.php` is the most common case. (And it's compatible with built-in debug server `php -S`), we've created that entry file for you in `public/index.php`

See [runner](runner) page for more runners (Swoole/ReactPHP and more).

## Routers (or just not) to use

`nikic/fastroute` is our default choice, it should work fine in most situation. But implement or integrate another route library is easy too.

Here we just continue with the fastroute, and next problem is where and how you call the register methods in `\FastRoute\RouteCollector`, a.k.a route discovery

## Route discovery

In the boilerplate, we just write a single method receiving `\FastRoute\RouteCollector`, and register all routes in there, this should be fine for small to medium project (maybe with help of `$routeCollector->addGroup`, or just breakdown into several smaller methods).

Another paradigm of route discovery, which is "distributed" (compared with centralized route discovery where you manage all routes in one place). You may use some filesystem iterator (glob or SPL RecursiveDirectoryIterator or `symfony/finder`) to traverse through you action class file (or other source of route), then get the route info from it somehow, then register it to `\FastRoute\RouteCollector`

## View Layer

If you are writing bare json api, the `$action->json()`  is there. But if your view layer is more complicated, here we talk about how to integrate a view engine that's already adapted to be used by lit application.

First require the package

```bash
composer require litphp/view-twig
```

Include the configuration (typically `configuration.php`, but don't forget to submit it as `configuration.dist.php`)

```php
$configuration += \Lit\View\Twig\TwigView::configuration(C::instance(\Twig\Loader\FilesystemLoader::class, [
    __DIR__ . '/templates' // your template path
]));
```

and use the builder method trait in `BaseAction`

```php
abstract class BaseAction extends BoltAbstractAction
{
    use TwigViewBuilderTrait;
    // ...
```

That's all setup, now we start write some template in `templates/index.twig`

```twig
Hello {{greetings}}
```

and change `HomeAction` to render it

```php
    protected function main(): ResponseInterface
    {
        // return new HtmlResponse(self::SOURCE);
        return $this->twig('index.twig')->render([
            'greetings' => 'TwigView',
        ]);
    }

```

You should now be able to see your first page rendered by twig in http://127.0.0.1:3080

We also provide [view-php](https://github.com/litphp/view-php) and [view-plates](https://github.com/litphp/view-plates). Both of them works similarly.

## Model / Service / Business Logic

We deliberately avoid to make any assumption here. You should write your own business logic in your class  and use DI (setter injector) to inject them in action class to use it.

You may check our [todobackend](https://github.com/litphp/todobackend) implementation, which use repository pattern to decouple data layer (PDO) with business logic.

