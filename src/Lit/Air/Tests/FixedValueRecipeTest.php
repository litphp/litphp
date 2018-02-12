<?php

declare(strict_types=1);

namespace Lit\Air\Tests;

use Lit\Air\Psr\Container;
use Lit\Air\Recipe\FixedValueRecipe;

class FixedValueRecipeTest extends AbstractTestCase
{
    public function testSmoke()
    {
        $key = self::randKey();
        $obj = new \stdClass();
        $stub = Container::value($obj);
        self::assertTrue($stub instanceof FixedValueRecipe);

        $returnValue = $this->container->define($key, $stub);
        self::assertSame($this->container, $returnValue, '->define return $this');
        $this->assertKeyExistWithValue($key, $obj);
    }
}
