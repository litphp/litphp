<?php namespace Nimo\Tests;

/**
 * User: mcfog
 * Date: 15/9/13
 */

use Nimo\Handlers\FixedResponseHandler;

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
