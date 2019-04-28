<?php

declare(strict_types=1);

namespace Lit\Nimo\Tests;

use Lit\Nimo\Middlewares\AbstractConditionMiddleware;
use Lit\Nimo\Middlewares\AbstractMiddleware;
use PHPUnit\Framework\Assert;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ConditionMiddlewareTest extends NimoTestCase
{
    public function testFalsyCondition()
    {
        $inner = $this->prophesize(AbstractMiddleware::class);
        $inner->__call('main', [])->shouldNotBeCalled();
        $request = $this->getRequestMock();
        $response = $this->getResponseMock();
        $handler = $this->assertedHandler($request, $response);

        $middleware = new class($inner->reveal(), $request, $handler) extends AbstractConditionMiddleware
        {
            protected $params;

            public function __construct(MiddlewareInterface $innerMiddleware, $request, $handler)
            {
                parent::__construct($innerMiddleware);
                $this->params = [$request, $handler];
            }

            protected function shouldRun(ServerRequestInterface $request, RequestHandlerInterface $handler): bool
            {
                [$request, $handler] = $this->params;
                Assert::assertSame($request, $this->request);
                Assert::assertSame($handler, $this->handler);
                return false;
            }
        };


        $returnValue = $middleware->process(
            $request,
            $handler
        );

        $this->assertSame($response, $returnValue);
    }

    public function testTruthyCondition()
    {
        $answerRes = $this->getMockForAbstractClass(ResponseInterface::class);
        $request = $this->getRequestMock();
        $expectedHandler = $this->throwHandler();
        $inner = $this->assertedMiddleware($request, $expectedHandler, $answerRes);

        $middleware = new class($inner, $request, $expectedHandler) extends AbstractConditionMiddleware
        {
            protected $params;

            public function __construct(MiddlewareInterface $innerMiddleware, $request, $handler)
            {
                parent::__construct($innerMiddleware);
                $this->params = [$request, $handler];
            }

            public function shouldRun(ServerRequestInterface $request, RequestHandlerInterface $handler): bool
            {
                [$request, $handler] = $this->params;
                Assert::assertSame($request, $this->request);
                Assert::assertSame($handler, $this->handler);
                return true;
            }
        };

        $returnValue = $middleware->process(
            $request,
            $expectedHandler
        );

        $this->assertSame($answerRes, $returnValue);
    }
}
