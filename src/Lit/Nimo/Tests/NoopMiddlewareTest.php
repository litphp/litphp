<?php

namespace Lit\Nimo\Tests;

use Lit\Nimo\Middlewares\NoopMiddleware;
use PHPUnit\Framework\Assert;

class NoopMiddlewareTest extends NimoTestCase
{

    public function testNoopMiddleware()
    {
        $requst = $this->getRequestMock();
        $response = $this->getResponseMock();
        $handler = $this->assertedHandler($requst, $response);
        $middleware = new NoopMiddleware();

        $actualResponse = $middleware->process($requst, $handler);
        Assert::assertSame($response, $actualResponse);
    }
}
