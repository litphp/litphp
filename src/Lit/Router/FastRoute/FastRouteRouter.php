<?php

declare(strict_types=1);

namespace Lit\Router\FastRoute;

use FastRoute\Dispatcher;
use Lit\Nimo\Handlers\CallableHandler;
use Lit\Router\FastRoute\ArgumentHandler\ArgumentHandlerInterface;
use Lit\Router\FastRoute\ArgumentHandler\RouteArgumentBag;
use Lit\Voltage\AbstractRouter;
use Lit\Voltage\Interfaces\RouterStubResolverInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class FastRouteRouter extends AbstractRouter
{
    /**
     * @var Dispatcher
     */
    protected $dispatcher;
    /**
     * @var mixed
     */
    protected $methodNotAllowed;
    /**
     * @var ArgumentHandlerInterface
     */
    protected $argumentHandler;

    public function __construct(
        Dispatcher $dispatcher,
        RouterStubResolverInterface $stubResolver,
        ArgumentHandlerInterface $argumentHandler = null,
        $notFound = null,
        $methodNotAllowed = null
    ) {
        parent::__construct($stubResolver, $notFound);
        $this->dispatcher = $dispatcher;
        $this->methodNotAllowed = $methodNotAllowed;
        $this->argumentHandler = $argumentHandler ?: new RouteArgumentBag();
    }

    protected function findStub(ServerRequestInterface $request)
    {
        $path = $request->getUri()->getPath();
        $method = $request->getMethod();

        $routeInfo = $this->dispatcher->dispatch($method, $path);

        return $this->parseRouteInfo($routeInfo);
    }

    protected function parseRouteInfo($routeInfo)
    {
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                return $this->notFound;
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                if (!empty($this->methodNotAllowed)) {
                    return [$this->methodNotAllowed, [$routeInfo[1]]];
                } else {
                    return $this->notFound;
                }
                break;
            case Dispatcher::FOUND:
                list(, $stub, $vars) = $routeInfo;

                if (empty($vars)) {
                    return $stub;
                } else {
                    return $this->proxy($stub, $vars);
                }
                break;

            default:
                throw new \Exception(__METHOD__ . '/' . __LINE__);
        }
    }

    protected function proxy($stub, array $vars)
    {
        $handle = function (ServerRequestInterface $request) use ($stub, $vars): ResponseInterface {
            $handler = $this->resolve($stub);
            $request = $this->argumentHandler->attachArguments($request, $vars);
            $response = $handler->handle($request);

            return $response;
        };

        return CallableHandler::wrap($handle);
    }
}
