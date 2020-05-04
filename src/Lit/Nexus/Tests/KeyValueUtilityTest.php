<?php

declare(strict_types=1);

namespace Lit\Nexus\Tests;

use Lit\Nexus\Derived\OffsetKeyValue;
use Lit\Nexus\Utilities\KeyValueUtility;
use PHPUnit\Framework\TestCase;

class KeyValueUtilityTest extends TestCase
{
    public function testGetOrSet()
    {
        $kv = OffsetKeyValue::wrap([
            'foo'=>new \stdClass(),
        ]);
        $val = KeyValueUtility::getOrSet($kv->slice('foo'), function() {
            self::fail('should not reach here');
        });
        self::assertSame($kv->get('foo'), $val);

        $bar = new \stdClass();
        $val2 = KeyValueUtility::getOrSet($kv->slice('baz'), function() use ($bar) {
            return $bar;
        });
        self::assertSame($bar, $val2);
        self::assertSame($bar, $kv->get('baz'));
    }
}
