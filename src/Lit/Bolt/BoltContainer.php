<?php namespace Lit\Bolt;

use Interop\Http\Factory\ResponseFactoryInterface;
use Lit\Air\Injection\SetterInjector;
use Lit\Air\Psr\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @property EventDispatcherInterface $events
 */
class BoltContainer extends Container
{
    public function __construct(?array $config = null)
    {
        parent::__construct(($config ?: []) + [
                Container::KEY_INJECTORS => function () {
                    return [
                        new SetterInjector(),
                    ];
                },
                static::class => $this,

                ResponseFactoryInterface::class => ['$' => 'autowire', BoltResponseFactory::class],
                'events' => ['$' => 'autowire', EventDispatcher::class],
            ]);
    }

    function __get($name)
    {
        return $this->get($name);
    }

    function __isset($name)
    {
        return $this->has($name);
    }
}
