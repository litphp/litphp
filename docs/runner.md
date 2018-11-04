---
id: runner
title: Runner and servers
sidebar_label: Runner and servers
---

Bolt can works under various different environments and any PSR-15/PSR-17 implementation. Runner is the abstraction layer of this. 

## SAPI

This is the "traditional" way of php application. If you are working an nginx+php-fpm or apache, then the `litphp/runner-zend-sapi` is for you.

Create a public directory under your project, and write an entry file 

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

BoltZendRunner::run(YOUR_CONFIGURATION);
```

then configure your web server point to the public directory, with all unknown request pointing to index.php, and you are done.

We use `zendframework/zend-diactoros` as PSR-15 implementation by this way.

## Other server containers

### Swoole HTTP

`litphp/runner-swoole` do the trick for you. Call `SwooleRunner::run` and run it with php.

### ReactPHP HTTP

You can use `litphp/runner-react` to run your application under ReactPHP. It's pretty much same: call `ReactRunner::run`

### Roadrunner

Roadrunner ([github](https://github.com/spiral/roadrunner)) is a relative new application server. It's based on a golang - php IPC bridge, the http server is running in golang (the `rr` binary), and call php process to do business logic.

So after you require `litphp/runner-road-runner`, and write an entry file calling `RoadRunnerWorker::run`, the entry file is a worker but not a http server. Write a `.rr.yaml` specify your entry file as worker, and fire `rr serve` to start the http server.

## Configuration

The configuration array fed into runner should be your DI (litphp/air) configuration. Entries needed by runners should all have some default value in runner. The only required entry is `BoltApp::class`, which defines you application.

