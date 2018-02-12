<?php namespace Nimo\Tests;

/**
 * User: mcfog
 * Date: 15/9/13
 */

use Nimo\Middlewares\FixedResponseMiddleware;

class FixedResponseMiddlewareTest extends NimoTestCase
{
    public function testFixedResponseMiddleware()
    {
        $answerRes = $this->getResponseMock();
        /** @noinspection PhpParamsInspection */
        $middleware = new FixedResponseMiddleware($answerRes);

        $returnValue = $middleware->process(
            $this->getRequestMock(),
            $this->throwHandler()
        );

        $this->assertSame($answerRes, $returnValue);
    }


}
