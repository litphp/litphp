<?php

declare(strict_types=1);

namespace Lit\Air\Tests;

use Lit\Air\Psr\Container;
use Lit\Air\Recipe\BuilderRecipe;
use PHPUnit\Framework\MockObject\MockObject;

class BuilderRecipeTest extends AirTestCase
{
    public function testSmoke()
    {
        $key = self::randKey();
        $obj = new \stdClass();

        /** @var MockObject $mock */
        $mock = $this
            ->getMockBuilder('mock')
            ->setMethods(['factory'])
            ->getMock();
        $mock->expects($this->once())
            ->method('factory')
            ->will($this->returnValue($obj));

        $stub = Container::builder([$mock, 'factory']);

        self::assertTrue($stub instanceof BuilderRecipe);

        $this->container->define($key, $stub);

        $this->assertKeyExistWithValue($key, $obj);
    }
}
