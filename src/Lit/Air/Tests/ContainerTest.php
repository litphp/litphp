<?php

declare(strict_types=1);

namespace Lit\Air\Tests;

use Lit\Air\Factory;
use Lit\Air\Psr\Container;
use Lit\Air\Recipe\FixedValueRecipe;
use Lit\Air\Recipe\RecipeInterface;

class ContainerTest extends AirTestCase
{
    public function testSetGet()
    {
        $key = self::randKey();
        $obj = new \stdClass();

        $this->assertKeyUnexist($key);

        $this->container->set($key, $obj);

        $this->assertKeyExistWithValue($key, $obj);

        $this->container->flush($key);

        $this->assertKeyUnexist($key);
    }

    public function testDelegation()
    {
        $key = self::randKey();
        $obj = new \stdClass();
        $this->container->set($key, $obj);

        $c2 = Container::wrap($this->container);
        self::assertTrue($c2->has($key));
        self::assertSame($obj, $c2->get($key));

        $c2->setDelegateContainer(new Container());
        self::assertFalse($c2->has($key));
    }

    public function testRecipeManagement()
    {
        $key = self::randKey();
        $recipe = self::createMock(RecipeInterface::class);
        $recipe->method('resolve')
            ->with($this->container, $key)
            ->willReturn($obj = new \stdClass());

        $this->container->define($key, $recipe);
        self::assertFalse($this->container->hasCacheEntry($key));
        self::assertTrue($this->container->has($key));

        self::assertKeyExistWithValue($key, $obj);
        self::assertFalse($this->container->hasCacheEntry($key));

        $this->container->set($key, $obj2 = new \stdClass());
        self::assertKeyExistWithValue($key, $obj2);
        $this->container->flush($key);

        $obj3 = new \stdClass();
        $this->container->extendRecipe($key, function ($recipe2) use ($obj3, $recipe) {
            self::assertSame($recipe2, $recipe);
            return new FixedValueRecipe($obj3);
        });
        self::assertKeyExistWithValue($key, $obj3);
    }
}
