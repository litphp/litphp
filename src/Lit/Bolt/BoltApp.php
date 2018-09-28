<?php

declare(strict_types=1);

namespace Lit\Bolt;

use Lit\Air\Factory;
use Lit\Bolt\Middlewares\ContextMiddleware;
use Lit\Bolt\Middlewares\EventMiddleware;
use Lit\Core\App;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class BoltApp extends App
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(
        ContainerInterface $container,
        RequestHandlerInterface $businessLogicHandler,
        MiddlewareInterface $middleware = null
    ) {
        $this->container = $container;
        parent::__construct($businessLogicHandler, $middleware);
    }

    protected function setup()
    {
        $factory = Factory::of($this->container);
        /** @noinspection PhpParamsInspection */
        $this->middlewarePipe
            ->prepend($factory->produce(EventMiddleware::class))
            ->prepend($factory->produce(ContextMiddleware::class));
    }
}
