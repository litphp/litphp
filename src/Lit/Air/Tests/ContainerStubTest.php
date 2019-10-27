<?php

namespace Lit\Air\Tests;

use Lit\Air\ContainerStub;

class ContainerStubTest extends AirTestCase
{
    public function testSmoke()
    {
        $result = ContainerStub::of(\stdClass::class)->instantiateFrom($this->container);
        self::assertInstanceOf(\stdClass::class, $result);

        $stub = ContainerStub::tryParse([
            Foo::class,
            [
                'bar' => $bar = new \stdClass(),
                'baz' => $baz = new \stdClass(),
            ],
        ]);
        assert($stub !== null);
        $result2 = $stub->instantiateFrom($this->container);
        self::assertInstanceOf(Foo::class, $result2);
        self::assertSame($bar, $result2->bar);
        self::assertSame($baz, $result2->baz);
    }
}
