<?php

declare(strict_types=1);

namespace Lit\Nimo\Tests;

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
