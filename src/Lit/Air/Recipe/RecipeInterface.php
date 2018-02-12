<?php

declare(strict_types=1);

namespace Lit\Air\Recipe;


use Lit\Air\WritableContainerInterface;

interface RecipeInterface
{
    public function resolve(WritableContainerInterface $container, ?string $id = null);
}
