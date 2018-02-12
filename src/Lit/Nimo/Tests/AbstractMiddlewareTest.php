<?php namespace Nimo\Tests;

/**
 * User: mcfog
 * Date: 15/9/13
 */

use Nimo\AbstractMiddleware;

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
