<?php

declare(strict_types=1);

namespace Lit\Bolt;

use Lit\Air\Configurator as C;
use Lit\Core\RouterDispatchHandler;
use Psr\Http\Server\RequestHandlerInterface;

class RouterConfiguration
{
    public static function default()
    {
        return [
            BoltApp::class => C::provideParameter([
                RequestHandlerInterface::class => C::produce(RouterDispatchHandler::class),
            ]),
        ];
    }
}
