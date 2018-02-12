<?php

declare(strict_types=1);

namespace Lit\Nimo\Interfaces;

use Psr\Http\Message\ServerRequestInterface;

interface RequestPredictionInterface
{
    public function isTrue(ServerRequestInterface $request): bool;
}
