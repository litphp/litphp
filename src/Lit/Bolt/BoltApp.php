<?php

declare(strict_types=1);

namespace Lit\Bolt;

use Lit\Bolt\Traits\ContainerAppTrait;
use Lit\Core\App;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class BoltApp extends App
{
    use ContainerAppTrait;

    public const MAIN_HANDLER = 'BoltApp::MAIN_HANDLER';

    public function __construct(BoltContainer $boltContainer, MiddlewareInterface $middleware = null)
    {
        $this->container = $boltContainer;
        /**
         * @var RequestHandlerInterface $businessLogicHandler
         */
        $businessLogicHandler = $boltContainer->get(self::MAIN_HANDLER);
        parent::__construct($businessLogicHandler, $middleware);
    }
}
