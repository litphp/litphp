<?php

declare(strict_types=1);

namespace Lit\Air\Recipe\Decorator;

use Lit\Air\Recipe\RecipeInterface;
use Lit\Air\Recipe\RecipeTrait;

/**
 * Decorator or wrapper recipe which change another recipe's behavior
 */
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

    /**
     * Decorates provided recipe.
     *
     * @param RecipeInterface $recipe The inner recipe.
     * @return AbstractRecipeDecorator A new recipe with decorated behavior
     */
    public static function decorate(RecipeInterface $recipe): self
    {
        return new static($recipe);
    }

    /**
     * Option setter
     *
     * @param mixed $option The option value.
     * @return AbstractRecipeDecorator
     */
    public function setOption($option): self
    {
        $this->option = $option;
        return $this;
    }
}
