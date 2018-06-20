<?php

declare(strict_types=1);

namespace Lit\Air\Tests;

use Lit\Air\Psr\Container;
use Lit\Air\Recipe\Decorator\CacheDecorator;

class CacheDecoratorRecipeTest extends AirTestCase
{
    public function testSmoke()
    {
        $key = self::randKey();
        $key2 = self::randKey();
        $obj = new \stdClass();
        $obj2 = new \stdClass();
        $counter = 0;
        $factory = function () use ($obj, &$counter) {
            $counter++;
            return $obj;
        };
        $stub = Container::builder($factory)->cached();

        self::assertTrue($stub instanceof CacheDecorator);

        $this->container->define($key, $stub);
        $this->container->define($key2, $stub);

        $this->assertKeyExistWithValue($key, $obj);
        self::assertSame(1, $counter, 'factory should be invoked once');
        $this->container->get($key);
        self::assertSame(1, $counter, 'factory should be invoked still once');
        $this->container->flush($key);
        $this->container->get($key);
        self::assertSame(2, $counter, 'factory should be invoked twice');

        //the cache is key-based, so same stub on different key use different cache entry
        $this->container->get($key2);
        self::assertSame(3, $counter, 'factory should be invoked 3 times');

        //re define the stub WONT flush the cache
        $this->container->define($key, Container::value($obj2));
        $this->assertKeyExistWithValue($key, $obj);
        self::assertSame(3, $counter, 'factory should be invoked 3 times');

        //but after a flush, things would change
        $this->container->flush($key);
        $this->assertKeyExistWithValue($key, $obj2);
    }
}
