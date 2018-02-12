<?php namespace Lit\Bolt\Traits;

use Lit\Air\Factory;
use Lit\Bolt\BoltContainer;
use Lit\Nexus\Utilities\Inspector;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequestFactory;

trait ContainerAppTrait
{
    /**
     * @var BoltContainer
     */
    protected $container;

    public static function run(BoltContainer $container)
    {
        Inspector::setGlobalHandler();
        $factory = Factory::of($container);
        $request = ServerRequestFactory::fromGlobals();
        $response = $factory->getOrProduce(static::class)->handle($request);

        $emitter = new SapiEmitter();
        $emitter->emit($response);
    }
}
