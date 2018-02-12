<?php namespace Lit\Nexus\Cache;

use Lit\Nexus\Interfaces\SingleValueInterface;
use Lit\Nexus\Traits\SingleValueTrait;
use Psr\Cache\CacheItemPoolInterface;

class CacheSingleValue implements SingleValueInterface
{
    use SingleValueTrait;
    /**
     * @var int|\DateInterval
     */
    protected $expire;
    /**
     * @var string
     */
    protected $key;
    /**
     * @var CacheItemPoolInterface
     */
    protected $cacheItemPool;

    /**
     * CacheSlicedValue constructor.
     * @param CacheItemPoolInterface $cacheItemPool
     * @param string $key
     * @param int|\DateInterval|null $expire
     */
    public function __construct(CacheItemPoolInterface $cacheItemPool, $key, $expire)
    {
        $this->expire = $expire;
        $this->key = $key;
        $this->cacheItemPool = $cacheItemPool;
    }
    
    public function get()
    {
        return $this->cacheItemPool->getItem($this->key)->get();
    }

    public function exists()
    {
        return $this->cacheItemPool->getItem($this->key)->isHit();
    }

    public function set($value)
    {
        $item = $this->cacheItemPool->getItem($this->key);
        $item->set($value);
        if (!empty($this->expire)) {
            $item->expiresAfter($this->expire);
        }

        $this->cacheItemPool->saveDeferred($item);
    }

    public function delete()
    {
        $this->cacheItemPool->deleteItem($this->key);
    }
}
