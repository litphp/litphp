<?php

declare(strict_types=1);

namespace Lit\Router\FastRoute;

use FastRoute\DataGenerator;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser;
use Lit\Nexus\Interfaces\SingleValueInterface;

class CachedDispatcher implements Dispatcher
{
    /**
     * @var Dispatcher
     */
    protected $dispatcher;
    /**
     * @var SingleValueInterface
     */
    protected $cache;
    /**
     * @var RouteParser
     */
    protected $routeParser;
    /**
     * @var DataGenerator
     */
    protected $dataGenerator;
    /**
     * @var
     */
    protected $dispatcherClass;
    /**
     * @var callable
     */
    protected $routeDefinition;

    /**
     * RouteDispatcher constructor.
     * @param SingleValueInterface $cache
     * @param RouteParser $routeParser
     * @param DataGenerator $dataGenerator
     * @param callable $routeDefinition
     * @param string $dispatcherClass
     * @internal param RouteCollector $routeCollector
     */
    public function __construct(
        SingleValueInterface $cache,
        RouteParser $routeParser,
        DataGenerator $dataGenerator,
        callable $routeDefinition,
        $dispatcherClass
    ) {
        $this->cache = $cache;
        $this->routeParser = $routeParser;
        $this->dataGenerator = $dataGenerator;
        $this->dispatcherClass = $dispatcherClass;
        $this->routeDefinition = $routeDefinition;
    }

    public function dispatch($httpMethod, $uri)
    {
        return $this->getDispatcher()->dispatch($httpMethod, $uri);
    }

    protected function getDispatcher()
    {
        if (!isset($this->dispatcher)) {
            if ($this->cache->exists()) {
                $data = $this->cache->get();
            } else {
                $collector = new RouteCollector($this->routeParser, $this->dataGenerator);
                call_user_func($this->routeDefinition, $collector);
                $data = $collector->getData();
                $this->cache->set($data);
            }
            $this->dispatcher = new $this->dispatcherClass($data);
        }
        return $this->dispatcher;
    }
}
