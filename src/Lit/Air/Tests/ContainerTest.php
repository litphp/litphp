<?php

declare(strict_types=1);

namespace Lit\Air\Tests;

use Lit\Air\Factory;
use Lit\Air\Psr\Container;

class ContainerTest extends AbstractTestCase
{
    public function testSetGet()
    {
        $key = self::randKey();
        $obj = new \stdClass();

        $this->assertKeyUnexist($key);

        $this->container->set($key, $obj);

        $this->assertKeyExistWithValue($key, $obj);

        $this->container->flush($key);

        $this->assertKeyUnexist($key);
    }

    public function testGetFactory()
    {
        self::assertInstanceOf(Factory::class, $this->container->get(Container::KEY_FACTORY));
    }
}
