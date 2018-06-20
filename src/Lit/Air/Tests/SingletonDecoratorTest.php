<?php

declare(strict_types=1);

namespace Lit\Air\Tests;

use Lit\Air\Configurator;
use Lit\Air\Psr\Container;
use Lit\Air\Recipe\BuilderRecipe;
use Lit\Air\Recipe\Decorator\SingletonDecorator;

class SingletonDecoratorTest extends AirTestCase
{
    public function testSmoke()
    {
        $key = self::randKey();
        $key2 = self::randKey();
        $obj = new \stdClass();
        $obj2 = new \stdClass();
        $counter = 0;
        $builder = function () use ($obj, &$counter) {
            $counter++;
            return $obj;
        };
        $stub = (new BuilderRecipe($builder))->singleton();
        $stub2 = Configurator::convertToRecipe($builder);

        self::assertTrue($stub instanceof SingletonDecorator);

        $this->container->define($key, $stub);

        $this->assertKeyExistWithValue($key, $obj);
        self::assertSame(1, $counter, 'factory should be invoked once');
        $this->container->get($key);
        self::assertSame(1, $counter, 'factory should be invoked still once');
        $this->container->flush($key);
        $this->container->get($key);
        self::assertSame(1, $counter, 'factory should be invoked still once');

        $this->container->define($key2, $stub);
        $this->container->get($key2);
        self::assertSame(1, $counter, 'factory should be invoked still once');

        $this->container->define($key2, $stub2);
        $this->container->get($key2);
        self::assertSame(2, $counter, 'factory should be invoked still once');

        //re define the stub affect immediately (the cache is on Stub class, not countainer)
        $this->container->define($key, Container::value($obj2));
        $this->assertKeyExistWithValue($key, $obj2);
    }
}
