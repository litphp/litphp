<?php

declare(strict_types=1);

namespace Lit\Nimo\Traits;

use Psr\Http\Message\ServerRequestInterface;

trait AttachToRequestTrait
{
    /**
     * @param ServerRequestInterface $request
     * @return static
     */
    public static function fromRequest(ServerRequestInterface $request)
    {
        /** @noinspection PhpUndefinedClassConstantInspection */
        $key = defined('static::ATTR_KEY') ? static::ATTR_KEY : static::class;
        if (!$instance = $request->getAttribute($key)) {
            throw new \RuntimeException('middleware not found:' . $key);
        }
        if (!$instance instanceof static) {
            throw new \RuntimeException('middleware class error:' . $key);
        }

        return $instance;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface
     */
    protected function attachToRequest(ServerRequestInterface $request = null): ServerRequestInterface
    {
        /**
         * @var ServerRequestInterface $request
         */
        /** @noinspection PhpUndefinedFieldInspection */
        $request = $request ?: $this->request;

        /** @noinspection PhpUndefinedClassConstantInspection */
        $key = defined('static::ATTR_KEY') ? static::ATTR_KEY : static::class;
        if ($request->getAttribute($key)) {
            throw new \RuntimeException('middleware collision:' . $key);
        }

        return $this->request = $request->withAttribute($key, $this);
    }
}
