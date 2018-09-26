<?php

namespace Lit\Nexus\Tests;

use Lit\Nexus\Derived\ObjectKeyValue;
use Lit\Nexus\Interfaces\KeyValueInterface;
use Lit\Nexus\Interfaces\ReadableKeyValueInterface;
use Lit\Nexus\Interfaces\ReadableSingleValueInterface;
use Lit\Nexus\Interfaces\SingleValueInterface;
use PHPUnit\Framework\TestCase;

class DerivedTest extends TestCase
{
    public function testFunction()
    {
        $kv = ObjectKeyValue::wrap((object)['foo' => new \stdClass(), 'bar!!baz' => new \stdClass()]);

        $prefixed = $kv->prefix('bar');
        self::assertFalse($prefixed->exists('foo'));
        self::assertTrue($prefixed->exists('baz'));
        self::assertSame($kv->get('bar!!baz'), $prefixed->get('baz'));

        $prefixed->set('nix', new \stdClass());
        self::assertTrue($kv->exists('bar!!nix'));
        self::assertSame($prefixed->get('nix'), $kv->get('bar!!nix'));

        $prefixed2 = $kv->prefix('bar', '!');
        self::assertSame($prefixed->get('baz'), $prefixed2->get('!baz'));

        $freezed = $kv->freeze();
        self::assertFalse($freezed instanceof KeyValueInterface);
        self::assertTrue($freezed instanceof ReadableKeyValueInterface);
        self::assertSame($kv->get('foo'), $freezed->get('foo'));
        self::assertFalse($freezed->exists('quz'));

        $val = $kv->slice('foo');
        self::assertTrue($val->exists());
        self::assertFalse($kv->slice('boo')->exists());

        self::assertSame($kv->get('foo'), $val->get());
        $obj = new \stdClass();
        $val->set($obj);
        self::assertSame($obj, $kv->get('foo'));

        $freezedVal = $val->freeze();
        self::assertTrue($freezedVal instanceof ReadableSingleValueInterface);
        self::assertFalse($freezedVal instanceof SingleValueInterface);
        self::assertTrue($freezedVal->exists());
        self::assertSame($val->get(), $freezedVal->get());
    }

}
