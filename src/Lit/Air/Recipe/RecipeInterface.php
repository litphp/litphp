<?php

declare(strict_types=1);

namespace Lit\Air\Recipe;

use Lit\Air\Psr\Container;

/**
 * RecipeInterface represents a dynamic entry in container. When the entry is fetched, the resolve method is invoked to
 * provide the concrete value.
 */
interface RecipeInterface
{
    /**
     * Provide the concrete value
     *
     * @param Container $container The container object.
     * @return mixed The concrete value.
     */
    public function resolve(Container $container);
}
