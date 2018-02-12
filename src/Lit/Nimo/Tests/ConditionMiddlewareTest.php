<?php namespace Nimo\Tests;

/**
 * User: mcfog
 * Date: 15/9/13
 */

use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Nimo\AbstractMiddleware;
use Nimo\Middlewares\AbstractConditionMiddleware;
use PHPUnit\Framework\Assert;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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

            public function shouldRun(ServerRequestInterface $request, RequestHandlerInterface $handler): bool
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
