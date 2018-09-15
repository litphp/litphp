<?php

declare(strict_types=1);

namespace Lit\Air\Recipe;

use Lit\Air\Factory;
use Psr\Container\ContainerInterface;

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

    /**
     * MultitonStub constructor.
     * @param callable $builder
     * @param array $extra
     */
    public function __construct(callable $builder, array $extra = [])
    {
        $this->builder = $builder;
        $this->extra = $extra;
    }

    public function resolve(ContainerInterface $container, ?string $id = null)
    {
        return Factory::of($container)->invoke($this->builder, $this->extra);
    }
}
