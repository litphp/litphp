<?php

namespace Lit\Bolt\Tests;

use FastRoute\RouteCollector;
use Lit\Air\Configurator;
use Lit\Air\Factory;
use Lit\Bolt\BoltApp;
use Lit\Bolt\Router\StubResolveException;
use Lit\Core\Interfaces\RouterInterface;
use Lit\Nexus\Traits\RememberConstructorParamTrait;
use Lit\Nimo\Handlers\CallableHandler;
use Lit\Router\FastRoute\FastRouteConfiguration;
use Lit\Router\FastRoute\FastRouteDefinition;
use Lit\Router\FastRoute\FastRouteRouter;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Request\ArraySerializer;
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

        $handler = CallableHandler::wrap(function (ServerRequestInterface $actualRequest) use ($response, $request) {
            self::assertEquals(
                ArraySerializer::toArray($request),
                ArraySerializer::toArray($actualRequest)
            );
            self::assertSame('42', $actualRequest->getAttribute('id'));

            return $response;
        });

        $response404 = new Response();
        $request404 = (new ServerRequest())
            ->withUri(new Uri('http://localhost/404'));

        $fooRequest = (new ServerRequest())
            ->withMethod('PUT')
            ->withUri(new Uri('http://localhost/foo'));

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
                        $routeCollector->put('/foo', VoidHandler::class);
                    }
                };
            },
        ];
        $config += FastRouteConfiguration::default();
        Configurator::config($this->container, $config);

        $factory = Factory::of($this->container);
        /**
         * @var BoltApp $app
         */
        $app = $factory->getOrProduce(BoltApp::class);

        $result = $app->handle($request);
        self::assertSame($result, $response);


        $result404 = $app->handle($request404);
        self::assertSame($result404, $response404);

        $router = $this->container->get(RouterInterface::class);
        self::assertInstanceOf(VoidHandler::class, $router->route($fooRequest));

        $router2 = $factory->instantiate(FastRouteRouter::class);
        try {
            $router2->route($request404);
            self::fail('shoulld throw');
        } catch (StubResolveException $exception) {
            self::assertSame(404, $exception->getResponse()->getStatusCode());
        }
    }
}
