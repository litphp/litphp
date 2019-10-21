<?php

declare(strict_types=1);

namespace Lit\Bolt;

use Lit\Air\Configurator as C;
use Lit\Voltage\Interfaces\RouterInterface;
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
     * @param mixed $router The router configuration.
     * @return array
     */
    public static function default($router = null): array
    {
        $config = [
            BoltApp::class => C::produce(BoltApp::class, [
                RequestHandlerInterface::class => C::alias(BoltApp::class, 'handler'),
                MiddlewareInterface::class => C::alias(BoltApp::class, 'middleware'),
            ]),
            C::join(BoltApp::class, 'handler') => C::instance(RouterDispatchHandler::class, [
                RouterInterface::class => C::alias(BoltApp::class, 'handler', 'router'),
            ]),
            C::join(BoltApp::class, 'middleware') => null,
        ];

        if ($router === null) {
            @trigger_error('$router argument will become required', E_USER_DEPRECATED);
        } else {
            $config[C::join(BoltApp::class, 'handler', 'router')] = $router;
        }

        return $config;
    }
}
