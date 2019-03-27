<?php

declare(strict_types=1);

namespace Lit\Air\Tests;

use Lit\Air\Factory;
use Lit\Air\Psr\CircularDependencyException;
use Lit\Air\Psr\Container;
use Lit\Air\Psr\ContainerException;
use Psr\Container\ContainerInterface;

class FactoryTest extends AirTestCase
{
    /**
     * @var Factory
     */
    protected $factory;

    public function testInvoke()
    {
        $obj = new \stdClass();
        $obj2 = $this->factory->produce(\stdClass::class);

        $returnValue = $this->factory->invoke(function () use ($obj) {
            return $obj;
        });
        self::assertSame($obj, $returnValue);

        $returnValue = $this->factory->invoke([Foo::class, 'identity'], [
            $obj
        ]);
        self::assertSame($obj, $returnValue);

        try {
            $this->factory->invoke([Foo::class, 'identity']);
            throw new \Exception('should not reach here');
        } catch (ContainerException $e) {

        }
        $this->container->set('!Method@Lit\Air\Tests\Foo::identity::', [$obj]);
        $returnValue = $this->factory->invoke([Foo::class, 'identity']);
        self::assertSame($obj, $returnValue);

        try {
            $this->factory->invoke('is_int');
            throw new \Exception('should not reach here');
        } catch (ContainerException $e) {

        }
        $this->container->set('!is_int::', [42]);
        self::assertTrue($this->factory->invoke('is_int'));

        $funcname = __NAMESPACE__ . '\identity';
        try {
            $this->factory->invoke($funcname);
            throw new \Exception('should not reach here');
        } catch (ContainerException $e) {

        }
        $this->container->set(sprintf('!%s::', $funcname), [$obj]);
        $returnValue = $this->factory->invoke($funcname);
        self::assertSame($obj, $returnValue);


        /** @noinspection PhpParamsInspection */
        $returnValue = $this->factory->invoke(new class
        {
            public function __invoke(\stdClass $foo)
            {
                return $foo;
            }
        });
        self::assertSame($obj2, $returnValue);

        $returnValue = $this->factory->invoke([
            new class
            {
                function foo($foo = 42)
                {
                    return $foo;
                }
            },
            'foo'
        ]);
        self::assertSame(42, $returnValue);


        $this->container->define(ContainerInterface::class, Container::value($this->container));
        $returnValue = $this->factory->invoke(function (ContainerInterface $foo) {
            return $foo;
        });
        self::assertSame($this->container, $returnValue);

        try {
            $this->factory->invoke(function ($foo) {
                return $foo;
            });
            self::fail('should throw');
        } catch (ContainerException $e) {
            //noop
        }
    }

    public function testInstantiate()
    {
        $obj = $this->factory->instantiate(\stdClass::class);
        self::assertInstanceOf(\stdClass::class, $obj);

        $obj2 = $this->factory->instantiate(\stdClass::class);
        self::assertNotSame($obj, $obj2);

        /**
         * @var \ArrayObject $arrObj
         */
        $arrObj = $this->factory->instantiate(\ArrayObject::class, [
            [1, 42],
            \ArrayObject::ARRAY_AS_PROPS,
            \RecursiveArrayIterator::class
        ]);
        self::assertEquals([1, 42], $arrObj->getArrayCopy());
        self::assertSame(\ArrayObject::ARRAY_AS_PROPS, $arrObj->getFlags());
        self::assertSame(\RecursiveArrayIterator::class, $arrObj->getIteratorClass());

        $foo = $this->factory->instantiate(Foo::class, [
            'bar' => $obj,
            \stdClass::class => $obj2
        ]);
        self::assertSame([
            'bar' => $obj,
            'baz' => $obj2,
            'qux' => 42,
            'ng' => null,
        ], get_object_vars($foo));

        $uniqid = uniqid();
        $this->container->set(Foo::class . '::', [
            'bar' => $obj2,
            'baz' => $obj,
            'ng' => $uniqid,
        ]);
        $foo = $this->factory->instantiate(Foo::class, [$obj, 2 => 9]);

        self::assertSame([
            'bar' => $obj,//$extra param override
            'baz' => $obj,
            'qux' => 9,
            'ng' => $uniqid,
        ], get_object_vars($foo));
    }

    public function testCircularDependency()
    {
        self::assertEquals(2, 1 + 1);
        try {
            $this->container->define(\ArrayObject::class, Container::builder([$this, '_circularFoo']))
                ->define(Foo::class, Container::autowire(null, [
                    'bar' => Container::builder(function (\ArrayObject $object) {
                        return get_class($object);
                    }),
                ]));
            $this->container->get(Foo::class);

            self::fail('should not success');
        } catch (CircularDependencyException $exception) {
            $stack = $exception->getStack();
            $this->assertInternalType('array', $stack);
        }
    }

    public function _circularFoo(Foo $foo)
    {
        $this->assertInstanceOf(Foo::class, $foo);
        return new \ArrayObject([1, 2, 3]);
    }

    protected function setUp()
    {
        parent::setUp();

        $this->factory = Factory::of($this->container);
    }
}

function identity($x) {
    return $x;
}