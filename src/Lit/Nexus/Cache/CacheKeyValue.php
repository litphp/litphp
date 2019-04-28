<?php

declare(strict_types=1);

namespace Lit\Nexus\Cache;

use Lit\Nexus\Interfaces\KeyValueInterface;
use Lit\Nexus\Interfaces\SingleValueInterface;
use Lit\Nexus\Traits\KeyValueTrait;
use Psr\Cache\CacheItemPoolInterface;

class CacheKeyValue implements KeyValueInterface
{
    use KeyValueTrait;
    /**
     * @var CacheItemPoolInterface
     */
    protected $cacheItemPool;

    protected $expire;

    /**
     * CacheKeyValue constructor.
     * @param CacheItemPoolInterface $cacheItemPool
     * @param int|\DateInterval|null $expire
     */
    public function __construct(CacheItemPoolInterface $cacheItemPool, $expire = null)
    {
        $this->cacheItemPool = $cacheItemPool;
        $this->expire = $expire;
    }

    public function set(string $key, $value)
    {
        $item = $this->cacheItemPool->getItem($key);
        $item->set($value);
        if (!empty($this->expire)) {
            $item->expiresAfter($this->expire);
        }

        $this->cacheItemPool->saveDeferred($item);
    }

    public function delete(string $key)
    {
        $this->cacheItemPool->deleteItem($key);
    }

    public function get(string $key)
    {
        return $this->cacheItemPool->getItem($key)->get();
    }

    public function exists(string $key)
    {
        return $this->cacheItemPool->getItem($key)->isHit();
    }

    /**
     * return sliced ISingleValue with overridden expire setting
     *
     * @param $key
     * @param int|\DateInterval $expire
     * @return SingleValueInterface
     */
    public function sliceExpire($key, $expire)
    {
        return new CacheSingleValue($this->cacheItemPool, $key, $expire);
    }
}
