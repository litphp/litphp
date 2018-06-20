<?php

namespace Lit\Air\Tests;

use Lit\Air\Recipe\FixedValueRecipe;

class CallbackDecoratorTest extends AirTestCase
{


    public function testSmoke()
    {
        $key = self::randKey();
        $inner = new FixedValueRecipe($obj = new \stdClass());
        $obj2 = new \stdClass();

        $wrapped = $inner->wrap(function ($delegate, $container, $key2) use ($key, $obj, $obj2) {
            self::assertTrue(is_callable($delegate));
            self::assertEquals($key, $key2);
            self::assertSame($obj, $delegate());
            return $obj2;
        });

        self::assertSame($obj2, $wrapped->resolve($this->container, $key));
    }
}
