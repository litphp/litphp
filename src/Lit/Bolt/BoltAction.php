<?php

declare(strict_types=1);

namespace Lit\Bolt;

use Lit\Air\Injection\SetterInjector;
use Lit\Core\Action;
use Psr\Http\Message\ResponseFactoryInterface;

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
