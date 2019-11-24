<?php

declare(strict_types=1);

namespace Lit\Air\Recipe;

use Lit\Air\Psr\Container;

/**
 * Get from another key when this recipe resolves.
 */
class AliasRecipe extends AbstractRecipe
{
    /**
     * @var string
     */
    protected $alias;

    public function __construct(string $alias)
    {
        $this->alias = $alias;
    }

    public function resolve(Container $container)
    {
        return $container->get($this->alias);
    }
}
