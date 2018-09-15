<?php

declare(strict_types=1);

namespace Lit\Air\Recipe;

use Lit\Air\WritableContainerInterface;

class AliasRecipe extends AbstractRecipe
{
    /**
     * @var string
     */
    protected $alias;

    /**
     * @param string $alias
     */
    public function __construct(string $alias)
    {
        $this->alias = $alias;
    }

    public function resolve(WritableContainerInterface $container, ?string $id = null)
    {
        return $container->get($this->alias);
    }
}
