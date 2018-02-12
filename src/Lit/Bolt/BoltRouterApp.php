<?php namespace Lit\Bolt;

use Psr\Http\Server\MiddlewareInterface;
use Lit\Bolt\Traits\ContainerAppTrait;
use Lit\Bolt\Traits\EventHookedAppTrait;
use Lit\Core\Interfaces\RouterInterface;
use Lit\Core\RouterApp;

class BoltRouterApp extends RouterApp
{
    use ContainerAppTrait;
    use EventHookedAppTrait;

    public function __construct(BoltContainer $container, MiddlewareInterface $middleware = null)
    {
        $this->container = $container;

        $router = $container->get(RouterInterface::class);
        /** @noinspection PhpParamsInspection */
        parent::__construct($router, $middleware);
    }
}
