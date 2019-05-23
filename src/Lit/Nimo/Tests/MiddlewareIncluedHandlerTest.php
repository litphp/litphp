<?php

namespace Lit\Nimo\Tests;

use Lit\Nimo\Handlers\CallableHandler;
use Lit\Nimo\Handlers\MiddlewareIncluedHandler;
use PHPUnit\Framework\Assert;

class MiddlewareIncluedHandlerTest extends NimoTestCase
{

    public function testMiddlewareIncluedHandler()
    {
        $wrappedHandler = $this->throwHandler();
        $request = $this->getRequestMock();
        $response = $this->getResponseMock();
        $middleware = $this->assertedMiddleware($request, $wrappedHandler, $response);

        /**
         * @var CallableHandler $wrappedHandler
         */
        $handler = $wrappedHandler->includeMiddleware($middleware);
        Assert::assertInstanceOf(MiddlewareIncluedHandler::class, $handler);

        $actualResponse = $handler->handle($request);
        Assert::assertSame($response, $actualResponse);
    }
}
