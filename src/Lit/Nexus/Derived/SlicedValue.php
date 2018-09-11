<?php

declare(strict_types=1);

namespace Lit\Nexus\Derived;

use Lit\Nexus\Interfaces\KeyValueInterface;
use Lit\Nexus\Interfaces\SingleValueInterface;
use Lit\Nexus\Traits\SingleValueTrait;

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

    /**
     * @param KeyValueInterface $keyValue
     * @param string $key
     */
    public function __construct(KeyValueInterface $keyValue, $key)
    {

        $this->keyValue = $keyValue;
        $this->key = $key;
    }

    /**
     * @param KeyValueInterface $keyValue
     * @param string $key
     * @return static
     */
    public static function slice(KeyValueInterface $keyValue, $key)
    {
        return new static($keyValue, $key);
    }

    /**
     * @return mixed
     */
    public function get()
    {
        return $this->keyValue->get($this->key);
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return $this->keyValue->exists($this->key);
    }

    /**
     * @param mixed $value
     * @return void
     */
    public function set($value)
    {
        $this->keyValue->set($this->key, $value);
    }

    /**
     * @return void
     */
    public function delete()
    {
        $this->keyValue->delete($this->key);
    }
}
 