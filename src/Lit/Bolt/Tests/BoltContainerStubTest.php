<?php

namespace Lit\Bolt\Tests;

use Lit\Air\Tests\Foo;
use Lit\Bolt\Router\BoltContainerStub;

class BoltContainerStubTest extends BoltTestCase
{
    public function testSmoke()
    {
        $result = BoltContainerStub::of(\stdClass::class)->instantiateFrom($this->container);
        self::assertInstanceOf(\stdClass::class, $result);

        $stub = BoltContainerStub::tryParse([
            Foo::class,
            [
                'bar' => $bar = new \stdClass(),
                'baz' => $baz = new \stdClass(),
            ]
        ]);
        assert($stub !== null);
        $result2 = $stub->instantiateFrom($this->container);
        self::assertInstanceOf(Foo::class, $result2);
        self::assertSame($bar, $result2->bar);
        self::assertSame($baz, $result2->baz);
    }
}
