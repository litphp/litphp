<?php

declare(strict_types=1);

namespace Lit\Bolt\Router;

use Lit\Air\Factory;
use Psr\Container\ContainerInterface;

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

    public function __construct(string $className, array $extraParameters = [])
    {
        $this->className = $className;
        $this->extraParameters = $extraParameters;
    }

    public static function of(string $className, array $extraParameters = [])
    {
        return new static($className, $extraParameters);
    }

    public static function tryParse($stub)
    {
        if (is_string($stub) && class_exists($stub)) {
            return static::of($stub);
        }

        //[$className, $params]
        if (is_array($stub) && count($stub) === 2 && class_exists($stub[0])) {
            return static::of($stub[0], $stub[1]);
        }

        throw new \RuntimeException("cannot understand stub");
    }

    public function produceFrom(ContainerInterface $container, $extraParameters = [])
    {
        return Factory::of($container)->produce($this->className, $extraParameters + $this->extraParameters);
    }

    public function instantiateFrom(ContainerInterface $container, $extraParameters = [])
    {
        return Factory::of($container)->instantiate($this->className, $extraParameters + $this->extraParameters);
    }
}
