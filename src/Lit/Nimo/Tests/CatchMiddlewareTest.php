<?php

namespace Lit\Nimo\Tests;

use Lit\Nimo\Handlers\CallableHandler;
use Lit\Nimo\Interfaces\ExceptionHandlerInterface;
use Lit\Nimo\Middlewares\CatchMiddleware;
use Lit\Nimo\Middlewares\NoopMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

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

        $ehandler = new class($response, $runtimeException, $request, $handler) implements ExceptionHandlerInterface
        {
            use RememberConstructorParamTrait;

            public function handle(
                \Throwable $exception,
                ServerRequestInterface $actualRequest,
                RequestHandlerInterface $orignalHandler,
                MiddlewareInterface $originalMiddleware
            ): ResponseInterface {
                [$response, $runtimeException, $request, $handler] = $this->params;
                CatchMiddlewareTest::assertSame($runtimeException, $exception);
                CatchMiddlewareTest::assertSame($request, $actualRequest);
                CatchMiddlewareTest::assertSame($handler, $orignalHandler);
                CatchMiddlewareTest::assertTrue($originalMiddleware instanceof NoopMiddleware);
                return $response;
            }
        };
        $catcher = CatchMiddleware::catcher($ehandler);
        $result = $catcher->process($request, $handler);
        self::assertSame($response, $result);
    }
}
