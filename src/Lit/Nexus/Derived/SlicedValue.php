<?php

declare(strict_types=1);

namespace Lit\Nexus\Derived;

use Lit\Nexus\Interfaces\KeyValueInterface;
use Lit\Nexus\Interfaces\SingleValueInterface;
use Lit\Nexus\Traits\SingleValueTrait;

/**
 * Derived class that given a KeyValueInterface object and a key, access its content
 */
class SlicedValue implements SingleValueInterface
{
    use SingleValueTrait;

    /**
     * @var KeyValueInterface
     */
    private $keyValue;
    /**
     * @var string
     */
    private $key;

    public function __construct(KeyValueInterface $keyValue, string $key)
    {

        $this->keyValue = $keyValue;
        $this->key = $key;
    }

    /**
     * Make a SingleValueInterface from the KeyValueInterface and key.
     *
     * @param KeyValueInterface $keyValue The KeyValueInterface object.
     * @param string            $key      The key.
     * @return static
     */
    public static function slice(KeyValueInterface $keyValue, string $key)
    {
        return new static($keyValue, $key);
    }

    public function get()
    {
        return $this->keyValue->get($this->key);
    }

    public function exists()
    {
        return $this->keyValue->exists($this->key);
    }

    public function set($value)
    {
        $this->keyValue->set($this->key, $value);
    }

    public function delete()
    {
        $this->keyValue->delete($this->key);
    }
}
