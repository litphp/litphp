<?php

declare(strict_types=1);

namespace Lit\Bolt;

use Lit\Air\Configurator as C;
use Lit\Voltage\RouterDispatchHandler;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RouterConfiguration
{
    public static function default()
    {
        return [
            BoltApp::class => C::provideParameter([
                RequestHandlerInterface::class => C::alias(BoltApp::class, RouterDispatchHandler::class),
                MiddlewareInterface::class => C::alias(BoltApp::class, MiddlewareInterface::class),
            ]),
            C::join(BoltApp::class, RouterDispatchHandler::class) => C::produce(RouterDispatchHandler::class),
            C::join(BoltApp::class, MiddlewareInterface::class) => null,
        ];
    }
}
