<?php

declare(strict_types=1);

namespace Lit\Router\FastRoute;

use FastRoute\DataGenerator;
use FastRoute\Dispatcher;
use FastRoute\RouteParser;
use Lit\Air\Configurator as C;
use Lit\Bolt\Router\BoltStubResolver;
use Lit\Bolt\RouterConfiguration;
use Lit\Core\Interfaces\RouterInterface;
use Lit\Core\Interfaces\RouterStubResolverInterface;
use Lit\Nexus\Void\VoidSingleValue;

class FastRouteConfiguration
{
    public static function default()
    {
        return [
                RouterInterface::class => C::singleton(FastRouteRouter::class),
                RouterStubResolverInterface::class => C::singleton(BoltStubResolver::class),

                DataGenerator::class => C::singleton(DataGenerator\GroupCountBased::class),
                RouteParser::class => C::singleton(RouteParser\Std::class),
                Dispatcher::class => C::singleton(
                    CachedDispatcher::class,
                    [
                        'cache' => new VoidSingleValue(),
                        'routeDefinition' => C::alias(FastRouteDefinition::class),
                        'dispatcherClass' => Dispatcher\GroupCountBased::class,
                    ]
                ),
            ] + RouterConfiguration::default();
    }
}
