<?php

declare(strict_types=1);

namespace Lit\Nimo\Middlewares;

use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Lit\Nimo\Interfaces\RequestPredictionInterface;
use Psr\Http\Message\ServerRequestInterface;

class PredictionWrapperMiddleware extends AbstractConditionMiddleware
{
    /**
     * @var RequestPredictionInterface
     */
    protected $requestPrediction;

    public function __construct(MiddlewareInterface $innerMiddleware, RequestPredictionInterface $requestPrediction)
    {
        parent::__construct($innerMiddleware);
        $this->requestPrediction = $requestPrediction;
    }

    public function shouldRun(ServerRequestInterface $request, RequestHandlerInterface $handler): bool
    {
        return $this->requestPrediction->isTrue($request);
    }
}
