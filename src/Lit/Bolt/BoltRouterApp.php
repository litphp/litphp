<?php

declare(strict_types=1);

namespace Lit\Bolt;

use Lit\Bolt\Traits\ContainerAppTrait;
use Lit\Core\Interfaces\RouterInterface;
use Lit\Core\RouterApp;
use Psr\Http\Server\MiddlewareInterface;

class BoltRouterApp extends RouterApp
{
    use ContainerAppTrait;

    public function __construct(BoltContainer $container, MiddlewareInterface $middleware = null)
    {
        $this->container = $container;

        $router = $container->get(RouterInterface::class);
        /** @noinspection PhpParamsInspection */
        parent::__construct($router, $middleware);
    }
}
