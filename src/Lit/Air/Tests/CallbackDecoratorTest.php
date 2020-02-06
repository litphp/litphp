<?php

namespace Lit\Air\Tests;

use Lit\Air\Recipe\FixedValueRecipe;

class CallbackDecoratorTest extends AirTestCase
{
    public function testSmoke()
    {
        $inner = new FixedValueRecipe($obj = new \stdClass());
        $obj2 = new \stdClass();

        $wrapped = $inner->wrap(function ($delegate, $container) use ($obj, $obj2) {
            self::assertTrue(is_callable($delegate));
            self::assertSame($obj, $delegate());
            return $obj2;
        });

        self::assertSame($obj2, $wrapped->resolve($this->container));
    }
}
