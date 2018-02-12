<?php namespace Lit\Bolt;

use Interop\Http\Server\MiddlewareInterface;
use Lit\Bolt\Traits\ContainerAppTrait;
use Lit\Bolt\Traits\EventHookedAppTrait;
use Lit\Core\App;

class BoltApp extends App
{
    use ContainerAppTrait;
    use EventHookedAppTrait;

    public const MAIN_HANDLER = 'BoltApp::MAIN_HANDLER';

    public function __construct(BoltContainer $boltContainer, MiddlewareInterface $middleware = null)
    {
        $this->container = $boltContainer;
        $businessLogicHandler = $boltContainer->get(self::MAIN_HANDLER);
        parent::__construct($businessLogicHandler, $middleware);
    }
}
