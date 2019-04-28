<?php

declare(strict_types=1);

namespace Lit\Nexus\Derived;

use Lit\Nexus\Interfaces\KeyValueInterface;
use Lit\Nexus\Traits\KeyValueTrait;

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

    protected function __construct(KeyValueInterface $store, $prefix)
    {
        if (empty($prefix)) {
            throw new \InvalidArgumentException();
        }

        $this->store = $store;
        $this->prefix = $prefix;
    }

    public static function wrap(KeyValueInterface $store, $prefix)
    {
        return new self($store, $prefix);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value)
    {
        $this->store->set($this->key($key), $value);
    }

    /**
     * @param string $key
     * @return void
     */
    public function delete(string $key)
    {
        $this->store->delete($this->key($key));
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->store->get($this->key($key));
    }

    /**
     * @param string $key
     * @return bool
     */
    public function exists(string $key)
    {
        return $this->store->exists($this->key($key));
    }

    protected function key($key)
    {
        return $this->prefix . $key;
    }
}
