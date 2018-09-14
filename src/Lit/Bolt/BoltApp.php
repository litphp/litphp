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

    public const MAIN_HANDLER = 'BoltApp::MAIN_HANDLER';

    public function __construct(Container $container, MiddlewareInterface $middleware = null)
    {
        $this->container = $container;
        /**
         * @var RequestHandlerInterface $businessLogicHandler
         */
        $businessLogicHandler = $container->get(self::MAIN_HANDLER);
        parent::__construct($businessLogicHandler, $middleware);
    }
}
