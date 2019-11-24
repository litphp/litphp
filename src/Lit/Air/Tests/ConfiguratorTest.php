<?php

namespace Lit\Air\Tests;

use Lit\Air\Configurator as C;
use Lit\Air\Recipe\Decorator\CallbackDecorator;
use Lit\Air\Recipe\FixedValueRecipe;

class ConfiguratorTest extends AirTestCase
{
    public function testBuilder()
    {
        $name = uniqid();
        $extra = [uniqid() => uniqid()];
        self::assertEquals([
            '$' => 'instance',
            $name,
            $extra,
        ], C::instance($name, $extra));

        $name = uniqid();
        $extra = [uniqid() => uniqid()];
        self::assertEquals([
            '$' => 'autowire',
            $name,
            $extra,
            false
        ], C::produce($name, $extra, false));

        $name = uniqid();
        $extra = [uniqid() => uniqid()];
        self::assertEquals([
            'decorator' => ['singleton' => true],
            '$' => 'instance',
            $name,
            $extra,
        ], C::singleton($name, $extra));

        $builder = function () {
        };
        $extra = [uniqid() => uniqid()];
        self::assertEquals([
            '$' => 'builder',
            $builder,
            $extra,
        ], C::builder($builder, $extra));

        $alias = uniqid();
        self::assertEquals([
            '$' => 'alias',
            $alias
        ], C::alias($alias));

        $value = uniqid();
        self::assertEquals([
            '$' => 'value',
            $value
        ], C::value($value));

        $name = uniqid();
        $extra = [uniqid() => uniqid()];
        $cb = function () {
        };
        $cfg = C::decorateCallback(C::singleton($name, $extra), $cb);
        self::assertEquals([
            'decorator' => ['singleton' => true, 'callback' => $cb],
            '$' => 'instance',
            $name,
            $extra,
        ], $cfg);
    }

    public function testConfig()
    {
        $val7 = new \stdClass();
        C::config($this->container, [
            $key1 = uniqid() => $val1 = uniqid(),
            $key2 = uniqid() => C::value($val2 = new \stdClass()),
            $key3 = uniqid() => new FixedValueRecipe($val3 = new \stdClass()),
            $key4 = C::class . '::' => [
                $key5 = uniqid() => C::value($val5 = new \stdClass()),
            ],
            $key6 = uniqid() => [
                '$' => 'value',
                $val6 = new \stdClass(),
                'decorator' => [
                    CallbackDecorator::class => function ($delegate, $container) use ($val7, $val6) {
                        self::assertTrue(is_callable($delegate));
                        self::assertSame($val6, $delegate());
                        $val6->answer = $val7;
                        return $val6;
                    }
                ],
            ],
        ]);

        self::assertEquals($val1, $this->container->get($key1));
        self::assertSame($val2, $this->container->get($key2));
        self::assertSame($val3, $this->container->get($key3));
        self::assertEquals([$key5 => $val5], $this->container->get($key4));

        self::assertSame($val6, $this->container->get($key6));
        self::assertSame($val7, $val6->answer);
    }
}
