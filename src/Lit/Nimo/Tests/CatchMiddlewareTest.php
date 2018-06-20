<?php

namespace Lit\Nimo\Tests;

use Lit\Nimo\Handlers\CallableHandler;
use Lit\Nimo\Middlewares\CatchMiddleware;

class CatchMiddlewareTest extends NimoTestCase
{
    public function testSmoke()
    {
        $runtimeException = new \RuntimeException();
        $handler = new CallableHandler(function () use ($runtimeException) {
            throw $runtimeException;
        });
        $response = $this->getResponseMock();

        $catcher = CatchMiddleware::catcher(function ($e) use ($response, $runtimeException) {
            self::assertSame($runtimeException, $e);
            return $response;
        });

        $result = $catcher->process($this->getRequestMock(), $handler);
        self::assertSame($response, $result);
    }
}
