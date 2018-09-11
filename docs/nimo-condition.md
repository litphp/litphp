---
id: nimo-condition
title: Conditional middleware
sidebar_label: Condition
---

Sometimes you may need to add some condition, so a middleware is only effective to part of the whole traffic.

There's a interface `RequestPredictionInterface` for this. 

```php
RequestPredictionInterface::isTrue(ServerRequestInterface $request): bool;
```

For example, a naiive prediction about whether the request is googlebot may look like this

```php
$isGooglebot = new class implements RequestPredictionInterface {
    public function isTrue(ServerRequestInterface $request): bool
    {
        return strpos($request->getHeaderLine('User-Agent'), 'Googlebot') !== false;
    }
};
```

Then you can wrap you middleware so 

```php
$prerenderContentForGooglebotMiddleware = $prerenderMiddleware->when($isGooglebot);

$excludeGoogleBotLoggerMiddleware = $loggerMiddleware->unless($isGooglebot); 
```

This is powered by `MiddlewareTrait::when`, `MiddlewareTrait::unless`, 
Without the trait, you can wrap your middleware by hand

```php
new PredictionWrapperMiddleware($prerenderMiddleware, $isGooglebot);

new PredictionWrapperMiddleware($loggerMiddleware, $isGooglebot, true); // use 3rd parameter to reverse the prediction
```
