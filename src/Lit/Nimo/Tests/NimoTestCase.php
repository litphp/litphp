<?php

declare(strict_types=1);

namespace Lit\Nimo\Tests;

use Lit\Nimo\AbstractHandler;
use Lit\Nimo\AbstractMiddleware;
use Lit\Nimo\Handlers\CallableHandler;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Request\ArraySerializer;

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

    protected function assertedHandler(
        ServerRequestInterface $expectedRequest,
        ResponseInterface $response,
        string $assertion = 'same'
    ) {
        return new class($expectedRequest, $response, $assertion) extends AbstractHandler
        {
            use RememberConstructorParamTrait;

            protected function main(): ResponseInterface
            {
                /** @noinspection PhpUndefinedFieldInspection */
                [$expectedRequest, $response, $assertion] = $this->params;
                if ($assertion === 'equal') {
                    Assert::assertEquals(
                        ArraySerializer::toArray($expectedRequest),
                        ArraySerializer::toArray($this->request)
                    );
                } else {
                    Assert::assertSame($expectedRequest, $this->request);
                }
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
