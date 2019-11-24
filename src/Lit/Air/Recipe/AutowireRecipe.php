<?php

declare(strict_types=1);

namespace Lit\Air\Recipe;

use Lit\Air\Factory;
use Lit\Air\Psr\Container;
use Lit\Air\Psr\ContainerException;

/**
 * Recipe that calls $factory->produce to resolve. a.k.a autowire.
 *
 * Since `produce` method will save the product under `$className` key in container and reuse it, it's singleton. If
 * you need multiple instance, use `InstanceRecipe` instead.
 */
class AutowireRecipe extends AbstractRecipe
{
    /**
     * @var null|string
     */
    protected $className;
    /**
     * @var array
     */
    protected $extra;
    /**
     * @var bool
     */
    protected $cached;

    public function __construct(?string $className = null, array $extra = [], bool $cached = true)
    {
        $this->className = $className;
        $this->extra = $extra;
        $this->cached = $cached;
    }

    public function resolve(Container $container)
    {
        $className = $this->className;
        if ($className === null || !class_exists($className)) {
            throw new ContainerException('unknown autowire class name');
        }

        if ($container->getRecipe($className) === $this) {
            // calling produce will cause infinite loop, break the chain here.
            $instance = Factory::of($container)->instantiate($className, $this->extra);
            if ($this->cached) {
                $container->set($className, $instance);
            }
            return $instance;
        }

        return Factory::of($container)->produce(
            /** @scrutinizer ignore-type */ $className,
            $this->extra,
            $this->cached
        );
    }
}
