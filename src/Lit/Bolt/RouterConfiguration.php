<?php

declare(strict_types=1);

namespace Lit\Bolt;

use Lit\Air\Configurator as C;
use Lit\Voltage\RouterDispatchHandler;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Configuration class for typical app using router.
 */
class RouterConfiguration
{
    /**
     * configuration for typical app using router
     *
     * @return array
     */
    public static function default(): array
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
