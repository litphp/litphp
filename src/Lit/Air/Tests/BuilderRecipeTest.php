<?php

declare(strict_types=1);

namespace Lit\Air\Tests;

use Lit\Air\Psr\Container;
use Lit\Air\Recipe\BuilderRecipe;

class BuilderRecipeTest extends AbstractTestCase
{
    public function testSmoke()
    {
        $key = self::randKey();
        $obj = new \stdClass();
        $counter = 0;
        $factory = function () use ($obj, &$counter) {
            $counter++;
            return $obj;
        };
        $stub = Container::builder($factory);

        self::assertTrue($stub instanceof BuilderRecipe);

        $this->container->define($key, $stub);

        $this->assertKeyExistWithValue($key, $obj);
        self::assertSame(1, $counter, 'factory should be invoked once');
        $this->container->get($key);
        self::assertSame(2, $counter, 'factory should be invoked twice');
    }
}
