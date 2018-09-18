<?php

declare(strict_types=1);

namespace Lit\Nimo\Middlewares;

use Lit\Nimo\Interfaces\RequestPredictionInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PredictionWrapperMiddleware extends AbstractConditionMiddleware
{
    /**
     * @var RequestPredictionInterface
     */
    protected $requestPrediction;
    /**
     * @var bool
     */
    protected $reverted;

    public function __construct(
        MiddlewareInterface $innerMiddleware,
        RequestPredictionInterface $requestPrediction,
        $reverted = false
    ) {
        parent::__construct($innerMiddleware);
        $this->requestPrediction = $requestPrediction;
        $this->reverted = $reverted;
    }

    public function shouldRun(ServerRequestInterface $request, RequestHandlerInterface $handler): bool
    {
        if ($this->reverted) {
            return !$this->requestPrediction->isTrue($request);
        } else {
            return $this->requestPrediction->isTrue($request);
        }
    }
}
