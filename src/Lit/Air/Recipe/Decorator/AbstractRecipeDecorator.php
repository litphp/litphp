<?php

declare(strict_types=1);

namespace Lit\Air\Recipe\Decorator;

use Lit\Air\Recipe\RecipeInterface;
use Lit\Air\Recipe\RecipeTrait;

abstract class AbstractRecipeDecorator implements RecipeInterface
{
    use RecipeTrait;
    /**
     * @var RecipeInterface
     */
    protected $recipe;
    protected $option;

    public function __construct(RecipeInterface $recipe)
    {
        $this->recipe = $recipe;
    }

    public static function decorate(RecipeInterface $recipe): self
    {
        return new static($recipe);
    }

    /**
     * @param mixed $option
     * @return AbstractRecipeDecorator
     */
    public function setOption($option): self
    {
        $this->option = $option;
        return $this;
    }
}
