<?php

declare(strict_types=1);

namespace Lit\Bolt;

use Lit\Air\Configurator as C;
use Lit\Core\RouterDispatchHandler;

class RouterConfiguration
{
    public static function default()
    {
        return [
            BoltApp::MAIN_HANDLER => C::produce(RouterDispatchHandler::class),
        ];
    }
}
