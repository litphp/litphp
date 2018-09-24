<?php
// @codeCoverageIgnoreStart

use FastRoute\RouteCollector;
use Lit\Bolt\BoltAbstractAction;
use Lit\Bolt\Zend\BoltRunner;
use Lit\Router\FastRoute\FastRouteConfiguration;
use Psr\Http\Message\ResponseInterface;

is_readable(__DIR__ . '/../vendor/autoload.php')
    ? require(__DIR__ . '/../vendor/autoload.php')
    : require(__DIR__ . '/../../../../vendor/autoload.php');

class ThrowResponseAction extends BoltAbstractAction
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

class FixedResponseAction extends BoltAbstractAction
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

$routes = function (RouteCollector $routeCollector) {
    $routeCollector->get('/test.json', FixedResponseAction::class);
    $routeCollector->get('/throw.json', ThrowResponseAction::class);
};

BoltRunner::run(FastRouteConfiguration::default($routes));

// @codeCoverageIgnoreEnd
