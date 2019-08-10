<?php

declare(strict_types=1);

namespace Lit\Bolt\Router;

use Lit\Air\Factory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

/**
 * Helper class for resolving stub with container
 */
class BoltContainerStub
{
    /**
     * @var string
     */
    protected $className;
    /**
     * @var array
     */
    protected $extraParameters;

    /**
     * BoltContainerStub constructor.
     *
     * @param string $className       Name of the class.
     * @param array  $extraParameters Extra params fed into DI factory.
     */
    public function __construct(string $className, array $extraParameters = [])
    {
        $this->className = $className;
        $this->extraParameters = $extraParameters;
    }

    /**
     * Shortcut method of constructor.
     *
     * @param string $className       Name of the class.
     * @param array  $extraParameters Extra params fed into DI factory.
     * @return BoltContainerStub
     */
    public static function of(string $className, array $extraParameters = []): self
    {
        return new static($className, $extraParameters);
    }

    /**
     * Try to parse the stub
     *
     * @param mixed $stub The stub.
     * @return BoltContainerStub|null
     */
    public static function tryParse($stub): ?self
    {
        if (is_string($stub) && class_exists($stub)) {
            return static::of($stub);
        }

        //[$className, $params]
        if (is_array($stub) && count($stub) === 2 && class_exists($stub[0])) {
            return static::of($stub[0], $stub[1]);
        }

        return null;
    }

    /**
     * Populate concrete product from given container
     *
     * @param ContainerInterface $container       The container.
     * @param array              $extraParameters Extra parameters.
     * @return object
     * @throws ContainerExceptionInterface Failed to produce.
     * @throws \ReflectionException Failed to produce.
     */
    public function instantiateFrom(ContainerInterface $container, array $extraParameters = [])
    {
        return Factory::of($container)->instantiate($this->className, $extraParameters + $this->extraParameters);
    }
}
