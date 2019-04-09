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

class FastRouteConfiguration
{
    public static function default($routeDefinition)
    {
        if (is_callable($routeDefinition)) {
            $routeDefinition = C::value($routeDefinition);
        }

        return [
                RouterInterface::class => C::alias(FastRouteRouter::class),

                FastRouteRouter::class => C::provideParameter([
                    Dispatcher::class => C::alias(Dispatcher::class),
                    RouterStubResolverInterface::class => C::singleton(BoltStubResolver::class),
                    'methodNotAllowed' => C::alias(FastRouteRouter::class, 'methodNotAllowed'),
                    'notFound' => C::alias(FastRouteRouter::class, 'notFound'),
                ]),
                C::join(FastRouteRouter::class, 'methodNotAllowed') => null,
                C::join(FastRouteRouter::class, 'notFound') => null,
                Dispatcher::class => C::singleton(
                    CachedDispatcher::class,
                    [
                        'cache' => C::alias(CachedDispatcher::class, 'cache'),
                        DataGenerator::class => C::alias(CachedDispatcher::class, DataGenerator::class),
                        RouteParser::class => C::alias(CachedDispatcher::class, RouteParser::class),
                        'routeDefinition' => C::alias(CachedDispatcher::class, 'routeDefinition'),
                        'dispatcherClass' => GCBDispatcher::class,
                    ]
                ),
                C::join(CachedDispatcher::class, 'cache') => C::produce(VoidSingleValue::class),
                C::join(CachedDispatcher::class, DataGenerator::class) => C::singleton(GCBDataGenerator::class),
                C::join(CachedDispatcher::class, RouteParser::class) => C::singleton(StdRouteParser::class),
                C::join(CachedDispatcher::class, 'routeDefinition') => $routeDefinition,
            ] + RouterConfiguration::default();
    }
}
