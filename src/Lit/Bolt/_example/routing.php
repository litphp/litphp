<?php

use FastRoute\RouteCollector;
use Lit\Air\Configurator;
use Lit\Bolt\BoltAction;
use Lit\Bolt\BoltContainer;
use Lit\Bolt\BoltRouterApp;
use Lit\Core\Interfaces\RouterInterface;
use Lit\Router\FastRoute\FastRouteConfiguration;
use Lit\Router\FastRoute\FastRouteDefinition;
use Lit\Router\FastRoute\FastRouteRouter;
use Psr\Http\Message\ResponseInterface;

is_readable(__DIR__ . '/../vendor/autoload.php')
    ? require(__DIR__ . '/../vendor/autoload.php')
    : require(__DIR__ . '/../../../../vendor/autoload.php');

class NotFoundAction extends BoltAction
{
    protected function main(): ResponseInterface
    {
        return $this->json()->render([
            'not' => 'found',
            'try' => ['/test.json', '/throw.json'],
            'method' => $this->request->getMethod(),
            'uri' => $this->request->getUri()->__toString(),
        ])->withStatus(404);
    }
}

class ThrowResponseAction extends BoltAction
{
    /**
     * @return ResponseInterface
     * @throws Exception
     * @throws \Lit\Core\ThrowableResponse
     */
    protected function main(): ResponseInterface
    {
        $this->callOtherMethod();
        throw new Exception('should never reach here');
    }

    /**
     * @throws \Lit\Core\ThrowableResponse
     */
    private function callOtherMethod()
    {
        self::throwResponse($this->json()->render([
            'msg' => 'this response is thrown instead of returned'
        ]));
    }
}

class FixedResponseAction extends BoltAction
{
    protected function main(): ResponseInterface
    {
        return $this->json()->render([
            'test.json' => 'should be this',
            'bool' => false,
            'nil' => null,
        ]);
    }
}

$config = [
    FastRouteDefinition::class => function () {
        return new class extends FastRouteDefinition
        {
            public function __invoke(RouteCollector $routeCollector): void
            {
                $routeCollector->get('/test.json', FixedResponseAction::class);
                $routeCollector->get('/throw.json', ThrowResponseAction::class);
            }
        };
    },
    RouterInterface::class => Configurator::singleton(FastRouteRouter::class, [
            'notFound' => NotFoundAction::class,
        ]
    ),
];

BoltRouterApp::run(new BoltContainer($config + FastRouteConfiguration::default()));
