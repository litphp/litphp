<?php

declare(strict_types=1);

namespace Lit\Air\Recipe;

use Lit\Air\Factory;
use Lit\Air\Psr\Container;

/**
 * Calls a builder method when resolved. The "builder method" will be invoked by $factory->invoke
 */
class BuilderRecipe extends AbstractRecipe
{
    /**
     * @var callable
     */
    protected $builder;
    /**
     * @var array
     */
    protected $extra;

    public function __construct(callable $builder, array $extra = [])
    {
        $this->builder = $builder;
        $this->extra = $extra;
    }

    public function resolve(Container $container)
    {
        return Factory::of($container)->invoke($this->builder, $this->extra);
    }
}
