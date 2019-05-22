<?php

declare(strict_types=1);

namespace Lit\Nimo\Tests;

use Lit\Nimo\Handlers\FixedResponseHandler;

class FixedResponseHandlerTest extends NimoTestCase
{
    public function testFixedResponseHandler()
    {
        $answerRes = $this->getResponseMock();
        /** @noinspection PhpParamsInspection */
        $handler = FixedResponseHandler::wrap($answerRes);

        $returnValue = $handler->handle(
            $this->getRequestMock()
        );

        $this->assertSame($answerRes, $returnValue);
    }


}
