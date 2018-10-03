<?php

declare(strict_types=1);

namespace Lit\Bolt;

use Lit\Air\Factory;
use Lit\Bolt\Middlewares\EventsHub;
use Lit\Bolt\Middlewares\RequestContext;
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

    /**
     * @return EventsHub
     */
    public function getEventsHub(): EventsHub
    {
        // factory produce is singletoned, so directly used in getter here
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Factory::of($this->container)->produce(EventsHub::class);
    }

    protected function setup()
    {
        $factory = Factory::of($this->container);
        /** @noinspection PhpParamsInspection */
        $this->middlewarePipe
            ->prepend($this->getEventsHub())
            ->prepend($factory->produce(RequestContext::class));
    }
}
