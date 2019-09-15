<?php

declare(strict_types=1);

namespace Lit\Nimo\Middlewares;

use Psr\Http\Message\ResponseInterface;

/**
 * A middleware that never delegate and always return a fixed response.
 */
class FixedResponseMiddleware extends AbstractMiddleware
{
    protected $fixedResponse;

    /**
     * @param ResponseInterface $response The fixed response.
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
