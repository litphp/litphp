<?php

declare(strict_types=1);

namespace Lit\Air\Recipe;

use Lit\Air\Factory;
use Lit\Air\Psr\Container;
use Lit\Air\Psr\ContainerException;

/**
 * Recipe that calls $container->instantiate to resolve.
 *
 * Since `instantiate` method only create instance, so this recipe will create multiple instance when resolved multiple
 * times. Can be decorated by SingletonDecorator to makes it a singleton.
 */
class InstanceRecipe extends AbstractRecipe
{
    /**
     * @var null|string
     */
    protected $className;
    /**
     * @var array
     */
    protected $extra;

    public function __construct(?string $className = null, array $extra = [])
    {
        $this->className = $className;
        $this->extra = $extra;
    }

    public function resolve(Container $container)
    {
        $className = $this->className;
        if ($className === null || !class_exists($className)) {
            throw new ContainerException('unknown autowire class name');
        }

        return Factory::of($container)->instantiate(
            /** @scrutinizer ignore-type */            $className,
            $this->extra
        );
    }
}
