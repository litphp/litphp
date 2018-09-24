<?php

namespace Lit\Bolt\Tests;

use Lit\Air\Factory;
use Lit\Air\Psr\Container;
use Lit\Bolt\BoltApp;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class BoltAppTest extends BoltTestCase
{
    public function testSmoke()
    {
        $response = new Response();
        $request = new ServerRequest();

        $this->container->define(BoltApp::class, Container::autowire(null, [
            RequestHandlerInterface::class => $this->assertedHandler($request, $response, 'equal')
        ]));
        $factory = Factory::of($this->container);
        $result = $factory->getOrProduce(BoltApp::class)->handle($request);

        self::assertSame($result, $response);
    }
}
