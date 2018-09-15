<?php

declare(strict_types=1);

namespace Lit\Air\Recipe;

use Lit\Air\Factory;
use Lit\Air\Psr\ContainerException;
use Psr\Container\ContainerInterface;

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

    public function resolve(ContainerInterface $container, ?string $id = null)
    {
        $className = is_null($this->className) ? $id : $this->className;
        if (!class_exists($className)) {
            throw new ContainerException('unknown autowire class name');
        }

        return Factory::of($container)->instantiate(
            /** @scrutinizer ignore-type */$className,
            $this->extra
        );
    }
}
