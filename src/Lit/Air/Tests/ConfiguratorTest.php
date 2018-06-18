<?php

namespace Lit\Air\Tests;

use Lit\Air\Configurator;
use Lit\Air\Recipe\FixedValueRecipe;

class ConfiguratorTest extends AbstractTestCase
{
    public function testBuilder()
    {
        $name = uniqid();
        $extra = [uniqid() => uniqid()];
        self::assertEquals([
            '$' => 'instance',
            $name,
            $extra,
        ], Configurator::instance($name, $extra));

        $name = uniqid();
        $extra = [uniqid() => uniqid()];
        self::assertEquals([
            '$' => 'autowire',
            $name,
            $extra,
        ], Configurator::produce($name, $extra));

        $extra = [uniqid() => uniqid()];
        self::assertEquals([
            '$' => 'autowire',
            null,
            $extra,
        ], Configurator::provideParameter($extra));

        $name = uniqid();
        $extra = [uniqid() => uniqid()];
        self::assertEquals([
            'decorator' => ['singleton' => true],
            '$' => 'instance',
            $name,
            $extra,
        ], Configurator::singleton($name, $extra));

        $builder = function () {
        };
        self::assertEquals([
            '$' => 'builder',
            $builder,
        ], Configurator::builder($builder));

        $alias = uniqid();
        self::assertEquals([
            '$' => 'alias',
            $alias
        ], Configurator::alias($alias));

        $value = uniqid();
        self::assertEquals([
            '$' => 'value',
            $value
        ], Configurator::value($value));

        $name = uniqid();
        $extra = [uniqid() => uniqid()];
        $cb = function () {
        };
        $cfg = Configurator::decorateCallback(Configurator::singleton($name, $extra), $cb);
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
        Configurator::config($this->container, [
            $key1 = uniqid() => $val1 = uniqid(),
            $key2 = uniqid() => Configurator::value($val2 = new \stdClass()),
            $key3 = uniqid() => new FixedValueRecipe($val3 = new \stdClass()),
            $key4 = Configurator::class . '::' => [
                $key5 = uniqid() => Configurator::value($val5 = new \stdClass()),
            ],
            $key6 = uniqid() => Configurator::decorateCallback(
                Configurator::value($val6 = new \stdClass()),
                function ($delegate, $container, $key) use ($val7, $val6, $key6) {
                    self::assertTrue(is_callable($delegate));
                    self::assertEquals($key6, $key);
                    self::assertSame($val6, $delegate());
                    $val6->answer = $val7;
                    return $val6;
                }),
        ]);

        self::assertEquals($val1, $this->container->get($key1));
        self::assertSame($val2, $this->container->get($key2));
        self::assertSame($val3, $this->container->get($key3));
        self::assertEquals([$key5 => $val5], $this->container->get($key4));

        self::assertSame($val6, $this->container->get($key6));
        self::assertSame($val7, $val6->answer);
    }


}
