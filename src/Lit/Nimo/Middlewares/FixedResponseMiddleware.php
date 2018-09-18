<?php

declare(strict_types=1);

namespace Lit\Nimo\Middlewares;

use Lit\Nimo\AbstractMiddleware;
use Psr\Http\Message\ResponseInterface;

class FixedResponseMiddleware extends AbstractMiddleware
{
    protected $fixedResponse;

    /**
     * @param ResponseInterface $response always return this response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->fixedResponse = $response;
    }

    protected function main(): ResponseInterface
    {
        return $this->fixedResponse;
    }
}
