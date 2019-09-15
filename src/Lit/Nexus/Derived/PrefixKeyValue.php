<?php

declare(strict_types=1);

namespace Lit\Nexus\Derived;

use Lit\Nexus\Interfaces\KeyValueInterface;
use Lit\Nexus\Traits\KeyValueTrait;

/**
 * Wraps another KeyValueInterface with fixed prefix.
 */
class PrefixKeyValue implements KeyValueInterface
{
    use KeyValueTrait;

    /**
     * @var KeyValueInterface
     */
    protected $store;
    /**
     * @var string
     */
    protected $prefix;

    protected function __construct(KeyValueInterface $store, string $prefix)
    {
        if (empty($prefix)) {
            throw new \InvalidArgumentException();
        }

        $this->store = $store;
        $this->prefix = $prefix;
    }

    /**
     * Return a new KeyValueInterface which always prepend prefix to the origin KeyValueInterface.
     *
     * @param KeyValueInterface $store  The KeyValueInterface object.
     * @param string            $prefix The prefix.
     * @return PrefixKeyValue
     */
    public static function wrap(KeyValueInterface $store, string $prefix)
    {
        return new self($store, $prefix);
    }

    public function set(string $key, $value)
    {
        $this->store->set($this->key($key), $value);
    }

    public function delete(string $key)
    {
        $this->store->delete($this->key($key));
    }

    public function get(string $key)
    {
        return $this->store->get($this->key($key));
    }

    public function exists(string $key)
    {
        return $this->store->exists($this->key($key));
    }

    protected function key($key)
    {
        return $this->prefix . $key;
    }
}
