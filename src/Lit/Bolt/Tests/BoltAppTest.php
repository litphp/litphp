<?php

namespace Lit\Bolt\Tests;

use Lit\Air\Factory;
use Lit\Bolt\BoltAbstractAction;
use Lit\Bolt\BoltApp;
use Lit\Voltage\ThrowableResponse;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class BoltAppTest extends BoltTestCase
{
    public function testSmoke()
    {
        $response = new Response();
        $request = new ServerRequest();

        $this->container->provideParameter(BoltApp::class, [
            RequestHandlerInterface::class => $this->assertedHandler($request, $response, 'equal'),
        ]);
        $factory = Factory::of($this->container);
        /** @var BoltApp $app */
        $app = $factory->produce(BoltApp::class);
        $result = $app->handle($request);

        self::assertSame($result, $response);
    }

    public function testThrow()
    {
        $response = new Response();
        $request = new ServerRequest();


        /** @var BoltAbstractAction|MockObject $action */
        $action = $this->getMockForAbstractClass(BoltAbstractAction::class);
        $action->method('main')
            ->with()
            ->willThrowException(ThrowableResponse::of($response));

        $this->container->provideParameter(BoltApp::class, [
            RequestHandlerInterface::class => $action,
        ]);
        $factory = Factory::of($this->container);
        /** @var BoltApp $app */
        $app = $factory->produce(BoltApp::class);
        $result = $app->handle($request);

        self::assertSame($result, $response);
    }
}
