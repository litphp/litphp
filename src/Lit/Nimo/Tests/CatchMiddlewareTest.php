<?php

namespace Lit\Nimo\Tests;

use Lit\Nimo\Handlers\CallableHandler;
use Lit\Nimo\Middlewares\NoopMiddleware;

class CatchMiddlewareTest extends NimoTestCase
{
    public function testSmoke()
    {
        $runtimeException = new \RuntimeException();
        $handler = new CallableHandler(function () use ($runtimeException) {
            throw $runtimeException;
        });
        $response = $this->getResponseMock();
        $request = $this->getRequestMock();

        $noop = new NoopMiddleware();

        $catcher = $noop->catch(function ($e, $req, $hdl, $mdw) use (
            $response,
            $runtimeException,
            $request,
            $handler,
            $noop
        ) {
            self::assertSame($runtimeException, $e);
            self::assertSame($request, $req);
            self::assertSame($handler, $hdl);
            self::assertSame($noop, $mdw);

            return $response;
        });

        $result = $catcher->process($request, $handler);
        self::assertSame($response, $result);
    }

}
