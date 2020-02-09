<?php

declare(strict_types=1);

namespace Lit\Air\Tests;

use Lit\Air\Factory;
use Lit\Air\Recipe\AbstractRecipe;

class AliasRecipeTest extends AirTestCase
{
    public function testSmoke()
    {
        $key = self::randKey();
        $key2 = self::randKey();
        $key3 = self::randKey();
        $obj = new \stdClass();
        $obj2 = Factory::of($this->container)->produce(\stdClass::class);

        $this->container
            ->set($key, $obj)
            ->define($key2, AbstractRecipe::alias($key))
            ->define($key3, AbstractRecipe::alias(\stdClass::class));

        $this->assertKeyExistWithValue($key2, $obj);
        $this->assertKeyExistWithValue($key3, $obj2);
    }
}
