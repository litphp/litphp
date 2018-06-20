<?php

namespace Lit\Bolt\Tests;

use Lit\Air\Injection\SetterInjector;
use Lit\Bolt\BoltAction;
use Lit\Bolt\BoltResponseFactory;
use Lit\Core\ThrowableResponse;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Diactoros\ServerRequest;

class BoltActionTest extends BoltTestCase
{
    public function testThrowResponse()
    {
        $response = new EmptyResponse();
        try {
            BoltAction::throwResponse($response);
        } catch (ThrowableResponse $throwableResponse) {
            self::assertSame($response, $throwableResponse->getResponse());
            return;
        }

        self::fail('should return in catch');
    }

    public function testSmoke()
    {
        $request = new ServerRequest();
        $response = new EmptyResponse();

        $action = $this->getMockForAbstractClass(BoltAction::class);
        $action->method('main')
            ->with()
            ->willReturn($response);

        $result = $action->handle($request);
        self::assertSame($response, $result);

        $factory = new BoltResponseFactory();
        self::assertEquals(SetterInjector::class, BoltAction::SETTER_INJECTOR);
        $reflectionObject = new \ReflectionObject($action);
        $reflectionProperty = $reflectionObject->getProperty('responseFactory');
        self::assertEquals(\ReflectionProperty::IS_PROTECTED, $reflectionProperty->getModifiers());
        $reflectionProperty->setAccessible(true);
        self::assertSame(null, $reflectionProperty->getValue($action));

        $action->injectResponseFactory($factory);
        self::assertSame($factory, $reflectionProperty->getValue($action));
    }
}
