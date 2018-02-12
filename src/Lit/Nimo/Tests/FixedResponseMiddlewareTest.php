<?php namespace Lit\Nimo\Tests;

/**
 * User: mcfog
 * Date: 15/9/13
 */

use Lit\Nimo\Middlewares\FixedResponseMiddleware;

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
