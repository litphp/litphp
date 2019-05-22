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
        $key = defined('static::ATTR_KEY') ? constant('static::ATTR_KEY') : static::class;
        if (!$instance = $request->getAttribute($key)) {
            throw new \RuntimeException('attribute empty:' . $key);
        }
        if (!$instance instanceof static) {
            throw new \RuntimeException('instance error:' . $key);
        }

        return $instance;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface
     */
    protected function attachToRequest(ServerRequestInterface $request = null): ServerRequestInterface
    {
        /** @noinspection PhpUndefinedFieldInspection */
        if (isset($this->request)) {
            $request = $request ?: $this->request;
        }
        assert($request instanceof ServerRequestInterface);

        $key = defined('static::ATTR_KEY') ? constant('static::ATTR_KEY') : static::class;
        if ($request->getAttribute($key)) {
            throw new \RuntimeException('attribute collision:' . $key);
        }

        $request = $request->withAttribute($key, $this);
        if (isset($this->request)) {
            $this->request = $request;
        }
        return $request;
    }
}
