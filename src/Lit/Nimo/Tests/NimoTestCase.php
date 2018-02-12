<?php namespace Nimo\Tests;

/**
 * User: mcfog
 * Date: 15/9/13
 */

use Interop\Http\Server\RequestHandlerInterface;
use Nimo\AbstractHandler;
use Nimo\AbstractMiddleware;
use Nimo\Handlers\CallableHandler;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class NimoTestCase extends TestCase
{
    protected function throwHandler()
    {
        return CallableHandler::wrap(function (ServerRequestInterface $request) {
            throw new \Exception('should throw');
        });
    }

    /**
     * @return ServerRequestInterface
     */
    protected function getRequestMock()
    {
        return $this->getMockForAbstractClass(ServerRequestInterface::class);
    }

    /**
     * @return ResponseInterface
     */
    protected function getResponseMock()
    {
        return $this->getMockForAbstractClass(ResponseInterface::class);
    }

    protected function assertedHandler(ServerRequestInterface $expectedRequest, ResponseInterface $response)
    {
        return new class($expectedRequest, $response) extends AbstractHandler
        {
            use RememberConstructorParamTrait;

            protected function main(): ResponseInterface
            {
                /** @noinspection PhpUndefinedFieldInspection */
                list($expectedRequest, $response) = $this->params;
                Assert::assertSame($expectedRequest, $this->request);
                return $response;
            }
        };

    }

    protected function assertedNoopMiddleware(
        ServerRequestInterface $expectedRequest,
        ServerRequestInterface $passedRequest = null
    ) {
        return new class($expectedRequest, $passedRequest) extends AbstractMiddleware
        {
            use RememberConstructorParamTrait;

            protected function main(): ResponseInterface
            {
                /** @noinspection PhpUndefinedFieldInspection */
                list($expectedRequest, $passedRequest) = $this->params;
                Assert::assertSame($expectedRequest, $this->request);
                return $this->delegate($passedRequest);
            }
        };
    }

    protected function assertedMiddleware(
        ServerRequestInterface $expectedRequest,
        RequestHandlerInterface $expectedHandler,
        ResponseInterface $response
    ) {
        return new class($expectedRequest, $expectedHandler, $response) extends AbstractMiddleware
        {
            use RememberConstructorParamTrait;

            protected function main(): ResponseInterface
            {
                /** @noinspection PhpUndefinedFieldInspection */
                list($expectedRequest, $expectedHandler, $response) = $this->params;
                Assert::assertSame($expectedRequest, $this->request);
                Assert::assertSame($expectedHandler, $this->handler);
                return $response;
            }
        };
    }

    protected function throwMiddleware(
        ServerRequestInterface $expectedRequest,
        RequestHandlerInterface $expectedHandler,
        \Throwable $throwable
    ) {
        return new class($expectedRequest, $expectedHandler, $throwable) extends AbstractMiddleware
        {
            use RememberConstructorParamTrait;

            protected function main(): ResponseInterface
            {
                /** @noinspection PhpUndefinedFieldInspection */
                list($expectedRequest, $expectedHandler, $throwable) = $this->params;
                Assert::assertSame($expectedRequest, $this->request);
                Assert::assertSame($expectedHandler, $this->handler);
                throw $throwable;
            }
        };
    }
}
