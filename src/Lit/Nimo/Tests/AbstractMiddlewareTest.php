<?php

declare(strict_types=1);

namespace Lit\Nimo\Tests;

use Lit\Nimo\AbstractMiddleware;

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

        /** @noinspection PhpParamsInspection */
        $returnValue = $middleware->process($req, $this->throwHandler());

        $this->assertSame($answerRes, $returnValue);
    }

}
