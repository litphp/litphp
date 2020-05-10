<?php

declare(strict_types=1);

namespace Lit\Runner\LaminasSapi;

use Laminas\HttpHandlerRunner\RequestHandlerRunner;
use Lit\Bolt\BoltContainerConfiguration;
use Psr\Container\ContainerInterface;

/**
 * App runner using zend HttpHandlerRunner
 */
class BoltLaminasRunner
{
    public static function run($config = [])
    {
        $container = $config instanceof ContainerInterface
            ? $config
            : BoltContainerConfiguration::createContainer($config + BoltLaminasConfiguration::default());

        /** @var RequestHandlerRunner $runner */
        $runner = $container->get(RequestHandlerRunner::class);
        $runner->run();
    }
}
