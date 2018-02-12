<?php

declare(strict_types=1);

namespace Nimo\Middlewares;

use Nimo\AbstractMiddleware;
use Psr\Http\Message\ResponseInterface;

/**
 * User: mcfog
 * Date: 15/9/4
 */
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
