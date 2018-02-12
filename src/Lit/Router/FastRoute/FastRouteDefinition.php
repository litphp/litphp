<?php

declare(strict_types=1);

namespace Lit\Router\FastRoute;

use FastRoute\RouteCollector;

abstract class FastRouteDefinition
{
    abstract public function __invoke(RouteCollector $routeCollector): void;
}
