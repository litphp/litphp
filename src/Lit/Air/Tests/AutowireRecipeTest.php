<?php

declare(strict_types=1);

namespace Lit\Air\Tests;

use Lit\Air\Psr\Container;
use Lit\Air\Recipe\AutowireRecipe;

class AutowireRecipeTest extends AbstractTestCase
{
    public function testSmoke()
    {
        $key = self::randKey();
        $obj = new \stdClass();
        $recipe = Container::autowire(Foo::class, [
            'bar' => $obj,
        ]);
        $this->assertInstanceOf(AutowireRecipe::class, $recipe);

        $this->container->define($key, $recipe);

        $instance = $this->container->get($key);
        $this->assertInstanceOf(Foo::class, $instance);
        $this->assertSame($obj, $instance->bar);
    }
}
