<?php

declare(strict_types=1);

namespace Lit\Router\FastRoute;

use FastRoute\DataGenerator;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser;
use Lit\Nexus\Interfaces\SingleValueInterface;

/**
 * FastRoute dispatcher wrapper with cache support
 */
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
     * @var string
     */
    protected $dispatcherClass;
    /**
     * @var callable
     */
    protected $routeDefinition;

    /**
     * RouteDispatcher constructor.
     *
     * @param SingleValueInterface $cache           The cache storage, should be able to serialize plain php array.
     * @param RouteParser          $routeParser     The routeParser.
     * @param DataGenerator        $dataGenerator   The dataGenerator.
     * @param callable             $routeDefinition The routeDefinition.
     * @param string               $dispatcherClass The dispatcher class name.
     */
    public function __construct(
        SingleValueInterface $cache,
        RouteParser $routeParser,
        DataGenerator $dataGenerator,
        callable $routeDefinition,
        string $dispatcherClass
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
