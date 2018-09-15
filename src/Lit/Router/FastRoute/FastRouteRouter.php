<?php

declare(strict_types=1);

namespace Lit\Router\FastRoute;

use FastRoute\Dispatcher;
use Lit\Core\AbstractRouter;
use Lit\Core\Interfaces\RouterStubResolverInterface;
use Lit\Nimo\Handlers\CallableHandler;
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

    public function __construct(
        Dispatcher $dispatcher,
        RouterStubResolverInterface $stubResolver,
        $methodNotAllowed = null,
        $notFound = null
    ) {
        parent::__construct($stubResolver, $notFound);
        $this->dispatcher = $dispatcher;
        $this->methodNotAllowed = $methodNotAllowed;
    }


    protected function findStub(ServerRequestInterface $request)
    {
        $path = $request->getUri()->getPath();
        $method = $request->getMethod();

        $routeInfo = $this->dispatcher->dispatch($method, $path);

        return $this->stub($routeInfo);
    }

    protected function stub($routeInfo)
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
        $handle = function (ServerRequestInterface $request) use ($stub, $vars) {
            foreach ($vars as $key => $val) {
                $request = $request->withAttribute($key, $val);
            }
            $handler = $this->resolve($stub);

            return $handler->handle($request);
        };

        return CallableHandler::wrap($handle);
    }
}
