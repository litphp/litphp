<?php

declare(strict_types=1);

namespace Lit\Router\FastRoute;

use FastRoute\DataGenerator;
use FastRoute\DataGenerator\GroupCountBased as GCBDataGenerator;
use FastRoute\Dispatcher;
use FastRoute\Dispatcher\GroupCountBased as GCBDispatcher;
use FastRoute\RouteParser;
use FastRoute\RouteParser\Std as StdRouteParser;
use Lit\Air\Configurator as C;
use Lit\Bolt\Router\BoltStubResolver;
use Lit\Bolt\RouterConfiguration;
use Lit\Nexus\Void\VoidSingleValue;
use Lit\Voltage\Interfaces\RouterInterface;
use Lit\Voltage\Interfaces\RouterStubResolverInterface;

/**
 * Configuration for FastRoute router application
 */
class FastRouteConfiguration
{
    public static function default($routeDefinition)
    {
        if (is_callable($routeDefinition)) {
            $routeDefinition = C::value($routeDefinition);
        }

        return [
                RouterInterface::class => C::alias(FastRouteRouter::class),

                FastRouteRouter::class => C::produce(FastRouteRouter::class, [
                    Dispatcher::class => C::alias(FastRouteRouter::class, 'dispatcher'),
                    RouterStubResolverInterface::class => C::alias(FastRouteRouter::class, 'stubResolver'),
                    'methodNotAllowed' => C::alias(FastRouteRouter::class, 'methodNotAllowed'),
                    'notFound' => C::alias(FastRouteRouter::class, 'notFound'),
                ]),
                C::join(FastRouteRouter::class, 'stubResolver') => C::singleton(BoltStubResolver::class),
                C::join(FastRouteRouter::class, 'methodNotAllowed') => null,
                C::join(FastRouteRouter::class, 'notFound') => null,
                C::join(FastRouteRouter::class, 'dispatcher') => C::singleton(
                    CachedDispatcher::class,
                    [
                        'cache' => C::alias(FastRouteRouter::class, 'dispatcher', 'cache'),
                        DataGenerator::class => C::alias(FastRouteRouter::class, 'dispatcher', 'dataGenerator'),
                        RouteParser::class => C::alias(FastRouteRouter::class, 'dispatcher', 'routeParser'),
                        'routeDefinition' => C::alias(FastRouteRouter::class, 'dispatcher', 'routeDefinition'),
                        'dispatcherClass' => C::alias(FastRouteRouter::class, 'dispatcher', 'dispatcherClass'),
                    ]
                ),

                C::join(FastRouteRouter::class, 'dispatcher', 'cache') => C::produce(VoidSingleValue::class),
                C::join(FastRouteRouter::class, 'dispatcher', 'dataGenerator') => C::singleton(GCBDataGenerator::class),
                C::join(FastRouteRouter::class, 'dispatcher', 'routeParser') => C::singleton(StdRouteParser::class),
                C::join(FastRouteRouter::class, 'dispatcher', 'routeDefinition') => $routeDefinition,
                C::join(FastRouteRouter::class, 'dispatcher', 'dispatcherClass') => GCBDispatcher::class,

            ] + RouterConfiguration::default(C::alias(FastRouteRouter::class));
    }
}
