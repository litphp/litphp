<?php

declare(strict_types=1);

namespace Lit\Air\Tests;

use Lit\Air\Factory;
use Lit\Air\Psr\Container;
use Lit\Air\Psr\NotFoundException;
use PHPUnit\Framework\TestCase;
use Psr\Container\NotFoundExceptionInterface;

abstract class AirTestCase extends TestCase
{

    /**
     * @var Container
     */
    protected $container;

    /**
     * @return string
     */
    protected static function randKey(): string
    {
        return md5(uniqid());
    }

    protected function setUp()
    {
        parent::setUp();
        $this->container = new Container();
    }

    /**
     * @param $key
     */
    protected function assertKeyUnexist($key): void
    {
        self::assertFalse($this->container->has($key), '->has should return false before the key set');
        try {
            $this->container->get($key);
            self::fail('->get should fail before key set');
        } catch (NotFoundException $e) {
            self::assertTrue(
                $e instanceof NotFoundExceptionInterface,
                '->get should throw not found exception and it should implement psr interface'
            );
        }
    }

    /**
     * @param $key
     * @param $obj
     */
    protected function assertKeyExistWithValue($key, $obj): void
    {
        self::assertTrue($this->container->has($key), '->has should return true after key set');
        self::assertSame($obj, $this->container->get($key), '->get should return the object previously set');
    }

    protected function getFactory(): Factory
    {
        return Factory::of($this->container);
    }
}
