<?php

declare(strict_types=1);

namespace Lit\Nimo\Traits;

use Psr\Http\Message\ServerRequestInterface;

/**
 * A common pattern that an object write itself to PSR request attributes.
 * By default the key will be the class name, can be override with class const `ATTR_KEY`.
 *
 * @property ServerRequestInterface $request
 */
trait AttachToRequestTrait
{
    /**
     * Get self from request (attributes).
     *
     * @param ServerRequestInterface $request The request.
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
     * Attach self to the request (in attributes) and return the new one.
     *
     * @param ServerRequestInterface $request The request.
     * @return ServerRequestInterface
     */
    protected function attachToRequest(ServerRequestInterface $request = null): ServerRequestInterface
    {
        if (property_exists($this, 'request')) {
            $request = $request ?: $this->request;
        }
        assert($request instanceof ServerRequestInterface);

        $key = defined('static::ATTR_KEY') ? constant('static::ATTR_KEY') : static::class;
        if ($request->getAttribute($key)) {
            throw new \RuntimeException('attribute collision:' . $key);
        }

        $request = $request->withAttribute($key, $this);
        if (property_exists($this, 'request')) {
            $this->request = $request;
        }
        return $request;
    }
}
