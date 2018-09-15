<?php

declare(strict_types=1);

namespace Lit\Bolt;

use Lit\Air\Psr\Container;
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
        Container $container,
        RequestHandlerInterface $businessLogicHandler,
        MiddlewareInterface $middleware = null
    ) {
        $this->container = $container;
        parent::__construct($businessLogicHandler, $middleware);
    }
}
