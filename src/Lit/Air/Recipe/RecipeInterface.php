<?php

declare(strict_types=1);

namespace Lit\Air\Recipe;

use Psr\Container\ContainerInterface;

/**
 * RecipeInterface represents a dynamic entry in container. When the entry is fetched, the resolve method is invoked to
 * provide the concrete value.
 */
interface RecipeInterface
{
    /**
     * Provide the concrete value
     *
     * @param ContainerInterface $container The container object.
     * @param string|null        $id        The key used to fetch, might be null when this recipe is not attached
     *                                      directly in container.
     * @return mixed The concrete value.
     */
    public function resolve(ContainerInterface $container, ?string $id = null);
}
