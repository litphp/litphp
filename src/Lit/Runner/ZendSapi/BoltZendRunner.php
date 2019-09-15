<?php

declare(strict_types=1);

namespace Lit\Runner\ZendSapi;

use Lit\Bolt\BoltContainerConfiguration;
use Psr\Container\ContainerInterface;
use Zend\HttpHandlerRunner\RequestHandlerRunner;

/**
 * App runner using zend HttpHandlerRunner
 */
class BoltZendRunner
{
    public static function run($config = [])
    {
        $container = $config instanceof ContainerInterface
            ? $config
            : BoltContainerConfiguration::createContainer($config + BoltZendConfiguration::default());

        /** @var RequestHandlerRunner $runner */
        $runner = $container->get(RequestHandlerRunner::class);
        $runner->run();
    }
}
