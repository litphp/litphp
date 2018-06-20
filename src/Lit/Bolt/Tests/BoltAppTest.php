<?php

namespace Lit\Bolt\Tests;

use Lit\Air\Factory;
use Lit\Bolt\BoltApp;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class BoltAppTest extends BoltTestCase
{
    public function testSmoke()
    {
        $response = new Response();
        $request = new ServerRequest();
        $this->container->set(
            BoltApp::MAIN_HANDLER,
            $this->assertedHandler($request, $response)
        );

        $factory = Factory::of($this->container);
        $result = $factory->getOrProduce(BoltApp::class)->handle($request);

        self::assertSame($result, $response);
    }
}
