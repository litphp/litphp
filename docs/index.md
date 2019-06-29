---
id: index
title: Documentation
sidebar_label: Introduction
---

LitPHP is a collection of components, here's a list of them.

| Name | Description | Based on |
| :--- | ---- | ---- |
| [**litphp/air**](https://github.com/litphp/air) | dependency injection | [PSR11](https://www.php-fig.org/psr/psr-11/) |
| [**litphp/nimo**](https://github.com/litphp/nimo) | middleware organizer | [PSR15](https://www.php-fig.org/psr/psr-15/) |
| [**litphp/voltage**](https://github.com/litphp/voltage) | fundamental application structure | litphp/nimo<br />[PSR17](https://www.php-fig.org/psr/psr-17/) |
| [**litphp/bolt**](https://github.com/litphp/bolt) | the microframework | litphp/voltage<br />litphp/air |
| [**litphp/middleware-ip-address**](https://github.com/litphp/middleware-ip-address) | a middleware retrieving request ip | litphp/nimo<br /> |
| [**litphp/middleware-cookie**](https://github.com/litphp/middleware-cookie) | a middleware read/write http cookies | litphp/nimo<br />dflydev/fig-cookies |
| [**litphp/router-fast-route**](https://github.com/litphp/router-fast-route) | a router that integrates FastRoute | litphp/voltage<br />nikic/fast-route |
| [**litphp/runner-zend-sapi**](https://github.com/litphp/runner-zend-sapi) | run you bolt app with zend-diactoros on standard SAPI | litphp/bolt<br />zendframework/zend-diactoros |
| [**litphp/runner-react**](https://github.com/litphp/runner-react) | run you bolt app with ReactPHP | litphp/bolt<br />react/http |
| [**litphp/runner-road-runner**](https://github.com/litphp/runner-road-runner) | run you bolt app with Roadrunner | litphp/bolt<br />spiral/roadrunner |
| [**litphp/runner-swoole**](https://github.com/litphp/runner-swoole) | run you bolt app with Swoole | litphp/bolt<br />swoole |
| [**litphp/nexus**](https://github.com/litphp/nexus) | utility / interface helps development | |
| [**litphp/view-plates**](https://github.com/litphp/view-plates) | use plates as templating engine| league/plates |
| [**litphp/view-php**](https://github.com/litphp/view-php) | use native php file as templating engine| slim/php-view |
| [**litphp/view-twig**](https://github.com/litphp/view-twig) | use twig as templating engine| twig/twig |

To start write your application, you should read [quick start](quickstart). There's a quick glance of [core concepts](concepts). To run your application under various environments, you should look at [this doc about runner](runner).

