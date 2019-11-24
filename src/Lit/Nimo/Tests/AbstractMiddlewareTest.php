<?php

declare(strict_types=1);

namespace Lit\Nimo\Tests;

use Lit\Nimo\Middlewares\AbstractMiddleware;

class AbstractMiddlewareTest extends NimoTestCase
{
    public function testMiddleware()
    {
        $answerRes = $this->getResponseMock();
        $req = $this->getRequestMock();

        $middleware = $this->getMockForAbstractClass(AbstractMiddleware::class);
        $middleware->expects(self::any())
            ->method('main')
            ->willReturn($answerRes);

        assert($middleware instanceof AbstractMiddleware);
        $returnValue = $middleware->process($req, $this->throwHandler());

        $this->assertSame($answerRes, $returnValue);
    }
}
