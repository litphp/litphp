<?php

declare(strict_types=1);

namespace Lit\Bolt;

use Lit\Air\Injection\SetterInjector;
use Lit\Air\Psr\Container;

class BoltContainerConfiguration
{
    public static function default()
    {
        return SetterInjector::configuration();
    }

    public static function createContainer(array $config = []): Container
    {
        return new Container($config + static::default());
    }
}
