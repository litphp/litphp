<?php

namespace Lit\Nexus\Tests;

use Lit\Nexus\Derived\OffsetKeyValue;
use PHPUnit\Framework\TestCase;

class OffsetKeyValueTest extends TestCase
{
    public function testFunction()
    {
        $arr = new \ArrayObject([new \stdClass(), 'key' => new \stdClass()]);

        $kv = OffsetKeyValue::wrap($arr);

        self::assertSame($arr, $kv->getContent());
        self::assertSame($arr[0], $kv->get('0'));
        self::assertSame($arr['key'], $kv->get('key'));

        self::assertFalse($kv->exists('42'));
        $level = error_reporting(E_NOTICE);
        try {
            $kv->get('42');
            self::fail('should throw');
        } catch (\Throwable $e) {
        }
        error_reporting(E_ALL ^ E_NOTICE);
        $kv->get('42');

        error_reporting($level);

        $value = new \stdClass();
        $kv->set('foo', $value);
        self::assertSame($value, $arr['foo']);
        self::assertSame($value, $kv->get('foo'));
        self::assertTrue($kv->exists('foo'));

        $kv->delete('key');
        self::assertFalse($kv->exists('key'));
        self::assertFalse(isset($arr['key']));
    }
}
