<?php

declare(strict_types=1);

namespace Lit\Nimo\Tests;

use Lit\Nimo\Interfaces\RequestPredictionInterface;
use Lit\Nimo\Middlewares\PredictionWrapperMiddleware;
use PHPUnit\Framework\Assert;
use Psr\Http\Message\ServerRequestInterface;

class PredictionWrapperMiddlewareTest extends NimoTestCase
{
    public function testPositiveCase()
    {
        $req = $this->getRequestMock();
        $res = $this->getResponseMock();
        $hdl = $this->throwHandler();
        $inner = $this->assertedMiddleware($req, $hdl, $res);

        $prediction = new class($req) implements RequestPredictionInterface
        {
            use RememberConstructorParamTrait;

            public function isTrue(ServerRequestInterface $request): bool
            {
                Assert::assertSame($this->params[0], $request);
                return true;
            }
        };

        $middleware = $inner->when($prediction);
        $actualResponse = $middleware->process($req, $hdl);
        Assert::assertSame($res, $actualResponse);

        try {
            $inner->unless($prediction)->process($req, $hdl);
            self::fail('should throw');
        } catch (\Exception $e) {
        }
    }

    public function testNegativeCase()
    {
        $req = $this->getRequestMock();
        $res = $this->getResponseMock();
        $hdl = $this->assertedHandler($req, $res);

        $falsePrediction = new class($req) implements RequestPredictionInterface
        {
            use RememberConstructorParamTrait;

            public function isTrue(ServerRequestInterface $request): bool
            {
                Assert::assertSame($this->params[0], $request);
                return false;
            }
        };

        $inner = $this->throwMiddleware($req, $hdl, new \Exception('should not call inner'));
        $middleware = new PredictionWrapperMiddleware($inner, $falsePrediction);

        $actualResponse = $middleware->process($req, $hdl);
        Assert::assertSame($res, $actualResponse);
    }
}
