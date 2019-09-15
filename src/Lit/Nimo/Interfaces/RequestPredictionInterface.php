<?php

declare(strict_types=1);

namespace Lit\Nimo\Interfaces;

use Psr\Http\Message\ServerRequestInterface;

/**
 * RequestPredictionInterface represents a boolean prediction about a response
 */
interface RequestPredictionInterface
{
    /**
     * Run the prediction and return whether it's matched or not
     *
     * @param ServerRequestInterface $request The request.
     * @return boolean
     */
    public function isTrue(ServerRequestInterface $request): bool;
}
