<?php

declare(strict_types=1);

namespace Lit\Nimo\Tests;

use Lit\Nimo\Middlewares\AbstractMiddleware;
use Lit\Nimo\Middlewares\MiddlewarePipe;
use PHPUnit\Framework\Assert;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\ServerRequest;

class MiddlewarePipeTest extends NimoTestCase
{
    public function testEmptyStack()
    {
        $stack = new MiddlewarePipe();
        $answerRes = $this->getResponseMock();
        $request = $this->getRequestMock();

        $handler = $this->assertedHandler($request, $answerRes);
        $res = $stack->process($request, $handler);
        self::assertSame($answerRes, $res);
    }

    public function testAppend()
    {
        $stack = new MiddlewarePipe();
        $request = $this->getRequestMock();
        $request2 = $this->getRequestMock();
        $request3 = $this->getRequestMock();
        $response = $this->getResponseMock();
        $middleware1 = $this->assertedNoopMiddleware($request, $request2);
        $middleware2 = $this->assertedNoopMiddleware($request2, $request3);
        $handler = $this->assertedHandler($request3, $response);

        $stack
            ->append($middleware1)
            ->append($middleware2);

        $res = $stack->process($request, $handler);
        self::assertSame($response, $res);

        $res = $middleware1
            ->append($middleware2)
            ->process($request, $handler);
        self::assertSame($response, $res);
    }

    public function testPrepend()
    {
        $stack = new MiddlewarePipe();
        $request = $this->getRequestMock();
        $request2 = $this->getRequestMock();
        $request3 = $this->getRequestMock();
        $response = $this->getResponseMock();
        $middleware1 = $this->assertedNoopMiddleware($request, $request2);
        $middleware2 = $this->assertedNoopMiddleware($request2, $request3);
        $handler = $this->assertedHandler($request3, $response);

        $stack
            ->prepend($middleware2)
            ->prepend($middleware1);

        $res = $stack->process($request, $handler);
        self::assertSame($response, $res);


        $res = $middleware2
            ->prepend($middleware1)
            ->process($request, $handler);
        self::assertSame($response, $res);
    }

    public function testRetryMiddleware()
    {
        $stack = new MiddlewarePipe();
        $request = $this->getRequestMock();
        $request2 = $this->getRequestMock();
        $response = $this->getResponseMock();
        $handler = $this->assertedHandler($request2, $response);

        $exception = new \Exception("please retry");
        $throwFirstTimeMiddleware = new class($exception, $request2) extends AbstractMiddleware
        {
            use RememberConstructorParamTrait;
            public $called = 0;

            protected function main(): ResponseInterface
            {
                [$exception, $request2] = $this->params;
                if ($this->called++ == 0) {
                    throw $exception;
                }
                return $this->delegate($request2);
            }
        };

        $retryMiddleware = new class($exception) extends AbstractMiddleware
        {
            use RememberConstructorParamTrait;

            protected function main(): ResponseInterface
            {
                [$exception] = $this->params;
                try {
                    $this->delegate();
                } catch (\Throwable $e) {
                    if ($e !== $exception) {
                        throw $e;
                    }

                    return $this->delegate();
                }

                Assert::fail('should never reach here.');
            }
        };

        $stack->append($retryMiddleware)
            ->append($throwFirstTimeMiddleware);

        $actualResponse = $stack->process($request, $handler);
        self::assertSame($response, $actualResponse);
        self::assertEquals(2, $throwFirstTimeMiddleware->called);
    }
}
