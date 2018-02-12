<?php namespace Lit\Bolt;

use Interop\Http\Factory\ResponseFactoryInterface;
use Zend\Diactoros\Response;

class BoltResponseFactory implements ResponseFactoryInterface
{
    public function createResponse($code = 200)
    {
        return (new Response())->withStatus($code);
    }
}
