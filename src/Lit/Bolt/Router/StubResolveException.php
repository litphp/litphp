<?php

declare(strict_types=1);

namespace Lit\Bolt\Router;

use Lit\Voltage\Interfaces\ThrowableResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Stub resolving exception
 */
class StubResolveException extends \RuntimeException implements ThrowableResponseInterface
{
    protected $stub;
    /**
     * @var ResponseFactoryInterface
     */
    protected $factory;

    /**
     * StubResolveException constructor.
     *
     * @param mixed                    $stub     The problematic stub.
     * @param ResponseFactoryInterface $factory  Response factory used to create not found response.
     * @param Throwable|null           $previous Previous exception.
     * @param string                   $message  Exception message.
     * @param integer                  $code     Exception code.
     */
    public function __construct(
        $stub,
        ResponseFactoryInterface $factory,
        Throwable $previous = null,
        string $message = 'router stub parse failed',
        int $code = 0
    ) {
        parent::__construct($message, $code, $previous);
        $this->stub = $stub;
        $this->factory = $factory;
    }

    /**
     * @return mixed
     */
    public function getStub()
    {
        return $this->stub;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        $response = $this->factory->createResponse(404);
        $response->getBody()->write('not found');

        return $response;
    }
}
