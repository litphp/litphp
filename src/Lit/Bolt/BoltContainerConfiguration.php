<?php

declare(strict_types=1);

namespace Lit\Bolt;

use Http\Factory\Diactoros\ResponseFactory;
use Lit\Air\Configurator as C;
use Lit\Air\Injection\SetterInjector;
use Lit\Air\Psr\Container;
use Psr\Http\Message\ResponseFactoryInterface;

class BoltContainerConfiguration
{
    public static function default()
    {
        return [
            Container::KEY_INJECTORS => C::value([
                new SetterInjector(),
            ]),
            ResponseFactoryInterface::class => C::produce(ResponseFactory::class),
        ];
    }

    public static function createContainer(array $config = []): Container
    {
        return new Container($config + static::default());
    }
}
