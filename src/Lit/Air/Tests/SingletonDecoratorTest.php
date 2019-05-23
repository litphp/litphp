<?php

declare(strict_types=1);

namespace Lit\Air\Tests;

use Lit\Air\Configurator;
use Lit\Air\Psr\Container;
use Lit\Air\Recipe\BuilderRecipe;
use Lit\Air\Recipe\Decorator\SingletonDecorator;
use PHPUnit\Framework\MockObject\MockObject;

class SingletonDecoratorTest extends AirTestCase
{
    public function testSingleton()
    {
        $key = self::randKey();
        $key2 = self::randKey();
        $obj = new \stdClass();
        $obj2 = new \stdClass();
        /** @var MockObject $mock */
        $mock = $this
            ->getMockBuilder('mock')
            ->setMethods(['factory'])
            ->getMock();
        $mock->expects($this->once())
            ->method('factory')
            ->will($this->returnValue($obj));

        $stub = (new BuilderRecipe([$mock, 'factory']))->singleton();

        self::assertTrue($stub instanceof SingletonDecorator);

        $this->container->define($key, $stub);

        $this->assertKeyExistWithValue($key, $obj);
        $this->container->get($key);
        $this->container->flush($key);
        $this->container->get($key);

        $this->container->define($key2, $stub);
        $this->container->get($key2);

        //re define the stub affect immediately (the cache is on Stub class, not countainer)
        $this->container->define($key, Container::value($obj2));
        $this->assertKeyExistWithValue($key, $obj2);
    }

    public function testReused()
    {
        $key = self::randKey();
        $key2 = self::randKey();
        $obj = new \stdClass();
        /** @var MockObject $mock */
        $mock = $this
            ->getMockBuilder('mock')
            ->setMethods(['factory'])
            ->getMock();
        $mock->expects($this->exactly(2))
            ->method('factory')
            ->will($this->returnValue($obj));

        $stub = (new BuilderRecipe([$mock, 'factory']))->singleton();
        $stub2 = Configurator::convertToRecipe([$mock, 'factory']);

        $this->container->define($key, $stub);
        $this->container->get($key);
        $this->container->define($key2, $stub2);
        $this->assertKeyExistWithValue($key2, $obj);
    }
}
