<?php namespace Lit\Bolt;

use Interop\Http\Factory\ResponseFactoryInterface;
use Lit\Air\Injection\SetterInjector;
use Lit\Core\Action;

abstract class BoltAction extends Action
{
    const SETTER_INJECTOR = SetterInjector::class;

    /**
     *
     * @param ResponseFactoryInterface $responseFactory
     * @return $this
     */
    public function injectResponseFactory(ResponseFactoryInterface $responseFactory): BoltAction
    {
        $this->responseFactory = $responseFactory;

        return $this;
    }
}
