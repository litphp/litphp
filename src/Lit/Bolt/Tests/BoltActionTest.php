<?php

namespace Lit\Bolt\Tests;

use Lit\Air\Injection\SetterInjector;
use Lit\Bolt\BoltAbstractAction;
use PHPUnit\Framework\MockObject\MockObject;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Diactoros\ResponseFactory;
use Zend\Diactoros\ServerRequest;

class BoltActionTest extends BoltTestCase
{
    public function testSmoke()
    {
        $request = new ServerRequest();
        $response = new EmptyResponse();

        /** @var BoltAbstractAction|MockObject $action */
        $action = $this->getMockForAbstractClass(BoltAbstractAction::class);
        $action->method('main')
            ->with()
            ->willReturn($response);

        $result = $action->handle($request);
        self::assertSame($response, $result);

        $factory = new ResponseFactory();
        self::assertEquals(SetterInjector::class, BoltAbstractAction::SETTER_INJECTOR);
        $reflectionObject = new \ReflectionObject($action);
        $reflectionProperty = $reflectionObject->getProperty('responseFactory');
        self::assertEquals(\ReflectionProperty::IS_PROTECTED, $reflectionProperty->getModifiers());
        $reflectionProperty->setAccessible(true);
        self::assertNull($reflectionProperty->getValue($action));

        $action->injectResponseFactory($factory);
        /** @var ResponseFactory $propValue */
        $propValue = $reflectionProperty->getValue($action);
        self::assertSame($factory, $propValue);
    }
}
