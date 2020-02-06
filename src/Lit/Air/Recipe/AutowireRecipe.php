<?php

declare(strict_types=1);

namespace Lit\Air\Recipe;

use Lit\Air\Factory;
use Lit\Air\Psr\Container;

/**
 * Recipe that calls $factory->produce to resolve. a.k.a autowire.
 */
class AutowireRecipe extends AbstractRecipe
{
    /**
     * @var string
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

    public function __construct(string $className, array $extra = [], bool $cached = true)
    {
        $this->className = $className;
        $this->extra = $extra;
        $this->cached = $cached;
    }

    public function resolve(Container $container)
    {
        $className = $this->className;
        if ($container->getRecipe($className) === $this) {
            // calling produce will cause infinite loop if the key is same as $classname, break it here.
            $instance = Factory::of($container)->instantiate($className, $this->extra);
            if ($this->cached) {
                $container->set($className, $instance);
            }
            return $instance;
        }

        return Factory::of($container)->produce(
            /** @scrutinizer ignore-type */            $className,
            $this->extra,
            $this->cached
        );
    }
}
