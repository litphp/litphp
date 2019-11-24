<?php

declare(strict_types=1);

namespace Lit\Bolt;

use Lit\Air\Injection\SetterInjector;
use Lit\Bolt\Middlewares\RequestContext;
use Lit\Voltage\AbstractAction;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * Base action class for bolt
 *
 * It's strongly recommended to have your own action base class extending this one
 */
abstract class BoltAbstractAction extends AbstractAction
{
    public const SETTER_INJECTOR = SetterInjector::class;

    /**
     * Injector for ResponseFactory
     *
     * @param ResponseFactoryInterface $responseFactory The ResponseFactory.
     * @return $this
     */
    public function injectResponseFactory(ResponseFactoryInterface $responseFactory): BoltAbstractAction
    {
        $this->responseFactory = $responseFactory;

        return $this;
    }

    protected function context(): RequestContext
    {
        return RequestContext::fromRequest($this->request);
    }
}
