<?php

declare(strict_types=1);

namespace Lit\Air\Recipe;

use Psr\Container\ContainerInterface;

interface RecipeInterface
{
    public function resolve(ContainerInterface $container, ?string $id = null);
}
