<?php

declare(strict_types=1);

namespace Lit\Air\Tests;

use Lit\Air\Injection\SetterInjector;
use Lit\Air\Psr\Container;
use Lit\Air\Recipe\AutowireRecipe;

class AutowireRecipeTest extends AirTestCase
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

        /**
         * @var Foo $instance
         */
        $instance = $this->container->get($key);
        $this->assertInstanceOf(Foo::class, $instance);
        $this->assertSame($obj, $instance->bar);
        self::assertEquals(null, $instance->getSplObjectStorage());

        $key2 = self::randKey();
        $this->getFactory()->addInjector(new SetterInjector());
        $this->container->define($key2, Container::instance(Foo::class, [
            'bar' => $obj,
        ]));
        $instance = $this->container->get($key2);
        self::assertInstanceOf(\SplObjectStorage::class, $instance->getSplObjectStorage());
    }
}
