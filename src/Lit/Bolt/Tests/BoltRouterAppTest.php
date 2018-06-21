<?php

namespace Lit\Bolt\Tests;

use FastRoute\RouteCollector;
use Lit\Air\Configurator;
use Lit\Air\Factory;
use Lit\Bolt\BoltRouterApp;
use Lit\Core\Interfaces\RouterInterface;
use Lit\Nexus\Traits\RememberConstructorParamTrait;
use Lit\Router\FastRoute\FastRouteConfiguration;
use Lit\Router\FastRoute\FastRouteDefinition;
use Lit\Router\FastRoute\FastRouteRouter;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;

class BoltRouterAppTest extends BoltTestCase
{

    public function testSmoke()
    {
        $response = new Response();

        $request = (new ServerRequest())
            ->withUri(new Uri('http://localhost/book/42/author'));
        $handler = $this->assertedHandler($request, $response, 'equal');

        $response404 = new Response();
        $request404 = (new ServerRequest())
            ->withUri(new Uri('http://localhost/404'));

        $config = [
            RouterInterface::class => Configurator::singleton(FastRouteRouter::class, [
                'notFound' => $response404,
            ]),
            FastRouteDefinition::class => function () use ($handler) {
                return new class($handler) extends FastRouteDefinition
                {
                    use RememberConstructorParamTrait;

                    public function __invoke(RouteCollector $routeCollector): void
                    {
                        [$handler] = $this->params;

                        $routeCollector->get('/book/{id:\d+}/author', $handler);
                    }
                };
            },
        ];
        $config += FastRouteConfiguration::default();
        Configurator::config($this->container, $config);

        $factory = Factory::of($this->container);
        /**
         * @var BoltRouterApp $app
         */
        $app = $factory->getOrProduce(BoltRouterApp::class);

        $result = $app->handle($request);
        self::assertSame($result, $response);

        $result404 = $app->handle($request404);
        self::assertSame($result404, $response404);
    }
}
