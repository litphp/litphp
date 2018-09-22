<?php

declare(strict_types=1);

namespace Lit\Nimo\Interfaces;

use Psr\Http\Message\ServerRequestInterface;

interface RequestPredictionInterface
{
    /**
     * Run the prediction and return whether it's matched or not
     *
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function isTrue(ServerRequestInterface $request): bool;
}
