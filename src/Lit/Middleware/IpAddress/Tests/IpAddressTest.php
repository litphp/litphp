<?php

namespace Lit\Middleware\IpAddress\Tests;

use Lit\Middleware\IpAddress\IpAddress;
use Lit\Nimo\Handlers\CallableHandler;
use Lit\Nimo\Tests\NimoTestCase;
use PHPUnit\Framework\Assert;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ServerRequestFactory;

class IpAddressTest extends NimoTestCase
{
    public function testIpSetByRemoteAddr()
    {
        $middleware = new IpAddress();

        $request = ServerRequestFactory::fromGlobals([
            'REMOTE_ADDR' => '192.168.1.1',
        ]);
        $this->runRequest($middleware, $request);

        Assert::assertSame('192.168.1.1', $middleware->getIpAddress());
    }

    public function testIpIsNullIfMissing()
    {
        $middleware = new IpAddress();

        $request = ServerRequestFactory::fromGlobals();
        $this->runRequest($middleware, $request);


        Assert::assertNull($middleware->getIpAddress());
    }

    public function testXForwardedForIp()
    {
        $middleware = new IpAddress(['192.168.1.1']);

        $request = ServerRequestFactory::fromGlobals([
            'REMOTE_ADDR' => '192.168.1.1',
            'HTTP_X_FORWARDED_FOR' => '192.168.1.3, 192.168.1.2, 192.168.1.1'
        ]);
        $this->runRequest($middleware, $request);

        Assert::assertSame('192.168.1.3', $middleware->getIpAddress());
    }

    public function testProxyIpIsIgnored()
    {
        $middleware = new IpAddress();

        $request = ServerRequestFactory::fromGlobals([
            'REMOTE_ADDR' => '192.168.0.1',
            'HTTP_X_FORWARDED_FOR' => '192.168.1.3, 192.168.1.2, 192.168.1.1'
        ]);
        $this->runRequest($middleware, $request);

        Assert::assertSame('192.168.0.1', $middleware->getIpAddress());
    }

    public function testHttpClientIp()
    {
        $middleware = new IpAddress(['192.168.1.1']);

        $request = ServerRequestFactory::fromGlobals([
            'REMOTE_ADDR' => '192.168.1.1',
            'HTTP_CLIENT_IP' => '192.168.1.3'
        ]);
        $this->runRequest($middleware, $request);

        Assert::assertSame('192.168.1.3', $middleware->getIpAddress());
    }

    public function testXForwardedForIpV6()
    {
        $middleware = new IpAddress(['192.168.1.1']);

        $request = ServerRequestFactory::fromGlobals([
            'REMOTE_ADDR' => '192.168.1.1',
            'HTTP_X_FORWARDED_FOR' => '001:DB8::21f:5bff:febf:ce22:8a2e'
        ]);
        $this->runRequest($middleware, $request);

        Assert::assertSame('001:DB8::21f:5bff:febf:ce22:8a2e', $middleware->getIpAddress());
    }

    public function testXForwardedForWithInvalidIp()
    {
        $middleware = new IpAddress(['192.168.1.1']);

        $request = ServerRequestFactory::fromGlobals([
            'REMOTE_ADDR' => '192.168.1.1',
            'HTTP_X_FORWARDED_FOR' => 'foo-bar'
        ]);
        $this->runRequest($middleware, $request);

        Assert::assertSame('192.168.1.1', $middleware->getIpAddress());
    }

    public function testXForwardedForIpWithTrustedProxy()
    {
        $middleware = new IpAddress(['192.168.0.1', '192.168.0.2']);

        $request = ServerRequestFactory::fromGlobals([
            'REMOTE_ADDR' => '192.168.0.2',
            'HTTP_X_FORWARDED_FOR' => '192.168.1.3, 192.168.1.2, 192.168.1.1'
        ]);
        $this->runRequest($middleware, $request);

        Assert::assertSame('192.168.1.3', $middleware->getIpAddress());
    }

    public function testXForwardedForIpWithUntrustedProxy()
    {
        $middleware = new IpAddress(['192.168.0.1']);

        $request = ServerRequestFactory::fromGlobals([
            'REMOTE_ADDR' => '192.168.0.2',
            'HTTP_X_FORWARDED_FOR' => '192.168.1.3, 192.168.1.2, 192.168.1.1'
        ]);
        $this->runRequest($middleware, $request);

        Assert::assertSame('192.168.0.2', $middleware->getIpAddress());
    }

    public function testForwardedWithMultipleFor()
    {
        $middleware = new IpAddress(['192.168.1.1']);

        $request = ServerRequestFactory::fromGlobals([
            'REMOTE_ADDR' => '192.168.1.1',
            'HTTP_FORWARDED' => 'for=192.0.2.43, for=198.51.100.17;by=203.0.113.60;proto=http;host=example.com',
        ]);
        $this->runRequest($middleware, $request);

        Assert::assertSame('192.0.2.43', $middleware->getIpAddress());
    }

    public function testForwardedWithAllOptions()
    {
        $middleware = new IpAddress(['192.168.1.1']);

        $request = ServerRequestFactory::fromGlobals([
            'REMOTE_ADDR' => '192.168.1.1',
            'HTTP_FORWARDED' => 'for=192.0.2.60; proto=http;by=203.0.113.43; host=_hiddenProxy, for=192.0.2.61',
        ]);
        $this->runRequest($middleware, $request);

        Assert::assertSame('192.0.2.60', $middleware->getIpAddress());
    }

    public function testForwardedWithWithIpV6()
    {
        $middleware = new IpAddress(['192.168.1.1']);

        $request = ServerRequestFactory::fromGlobals([
            'REMOTE_ADDR' => '192.168.1.1',
            'HTTP_FORWARDED' => 'For="[2001:db8:cafe::17]:4711", for=_internalProxy',
        ]);
        $this->runRequest($middleware, $request);

        Assert::assertSame('2001:db8:cafe::17', $middleware->getIpAddress());
    }

    public function testCustomHeader()
    {
        $headersToInspect = [
            'Foo-Bar'
        ];
        $middleware = new IpAddress(['192.168.1.1'], $headersToInspect);

        $request = ServerRequestFactory::fromGlobals([
            'REMOTE_ADDR' => '192.168.1.1',
        ]);
        $request = $request->withAddedHeader('Foo-Bar', '192.168.1.3');
        $this->runRequest($middleware, $request);

        Assert::assertSame('192.168.1.3', $middleware->getIpAddress());
    }

    /**
     * @param $middleware
     * @param $request
     */
    protected function runRequest(IpAddress $middleware, ServerRequestInterface $request): void
    {
        $response = $this->getResponseMock();
        $handler = CallableHandler::wrap(function (ServerRequestInterface $request) use ($response, $middleware) {
            $actualMiddleware = IpAddress::fromRequest($request);
            Assert::assertSame($middleware, $actualMiddleware);

            return $response;
        });
        $acturlResponse = $middleware->process($request, $handler);
        Assert::assertSame($response, $acturlResponse);
    }
}
