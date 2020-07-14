<?php

declare(strict_types=1);

namespace Lit\Bolt;

use Lit\Air\Configurator as C;
use Lit\Voltage\Interfaces\RouterInterface;
use Lit\Voltage\RouterDispatchHandler;

/**
 * Configuration class for typical app using router.
 */
class RouterConfiguration
{
    /**
     * configuration for typical app using router
     *
     * @param mixed $router     The router configuration.
     * @param mixed $middleware The middleware configuration.
     * @return array
     */
    public static function default($router, $middleware = null): array
    {
        $handler = C::instance(RouterDispatchHandler::class, [
            RouterInterface::class => C::alias(BoltApp::class, 'handler', 'router'),
        ]);
        $config = BoltApp::configuration($handler, $middleware);
        $config[C::join(BoltApp::class, 'handler', 'router')] = $router;

        return $config;
    }
}
