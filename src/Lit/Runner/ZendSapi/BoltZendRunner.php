<?php

declare(strict_types=1);

namespace Lit\Runner\ZendSapi;

use Lit\Bolt\BoltContainerConfiguration;
use Zend\HttpHandlerRunner\RequestHandlerRunner;

class BoltZendRunner
{
    public static function run($config = [])
    {
        $container = BoltContainerConfiguration::createContainer($config + BoltZendConfiguration::default());
        /** @var RequestHandlerRunner $runner */
        $runner = $container->get(RequestHandlerRunner::class);
        $runner->run();
    }
}
