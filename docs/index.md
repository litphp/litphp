---
id: index
title: Documentation
sidebar_label: Introduction
---

LitPHP is a collection of components, here's a list of them.

| Name | Description | Based on |
| :--- | ---- | ---- |
| **litphp/air** | dependency injection | [PSR11](https://www.php-fig.org/psr/psr-11/) |
| **litphp/nimo** | middleware organizer | [PSR15](https://www.php-fig.org/psr/psr-15/) |
| **litphp/voltage** | fundamental application structure | litphp/nimo<br />[PSR17](https://www.php-fig.org/psr/psr-17/) |
| **litphp/bolt** | the microframework | litphp/voltage<br />litphp/air |
| **litphp/middleware-ip-address** | a middleware retrieving request ip | litphp/nimo<br /> |
| **litphp/middleware-cookie** | a middleware read/write http cookies | litphp/nimo<br />dflydev/fig-cookies |
| **litphp/router-fast-route** | a router that integrates FastRoute | litphp/voltage<br />nikic/fast-route |
| **litphp/runner-zend-sapi** | run you bolt app with zend-diactoros on standard SAPI | litphp/bolt<br />zendframework/zend-diactoros |
| **litphp/nexus** | utility / interface helps development | |

To start write your application, you should read [quick start](quickstart.md).

To run your application, you should look at [this doc about runner](runner.md).