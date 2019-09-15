<?php

declare(strict_types=1);

namespace Lit\Router\FastRoute;

use FastRoute\RouteCollector;

/**
 * Base class for a invokable object which can be used as route definition.
 */
abstract class FastRouteDefinition
{
    abstract public function __invoke(RouteCollector $routeCollector): void;
}
