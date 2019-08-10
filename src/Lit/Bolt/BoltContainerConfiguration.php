<?php

declare(strict_types=1);

namespace Lit\Bolt;

use Lit\Air\Injection\SetterInjector;
use Lit\Air\Psr\Container;

/**
 * Configuration class for Bolt
 */
class BoltContainerConfiguration
{
    /**
     * Configuration for Bolt.
     *
     * @return array
     */
    public static function default()
    {
        return SetterInjector::configuration();
    }

    /**
     * Create a container instance.
     *
     * @param array $config The configuration array.
     * @return Container
     */
    public static function createContainer(array $config = []): Container
    {
        return new Container($config + static::default());
    }
}
