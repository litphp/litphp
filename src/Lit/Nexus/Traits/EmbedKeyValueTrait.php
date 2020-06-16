<?php

declare(strict_types=1);

namespace Lit\Nexus\Traits;

use Lit\Nexus\Interfaces\KeyValueInterface;

/**
 * KeyValueInterface implementation which delegating to $innerKeyValue
 */
trait EmbedKeyValueTrait
{
    /**
     * @var KeyValueInterface
     */
    protected $innerKeyValue;

    public function set(string $key, $value)
    {
        $this->innerKeyValue->set($key, $value);
    }

    public function delete(string $key)
    {
        $this->innerKeyValue->delete($key);
    }

    public function get(string $key)
    {
        return $this->innerKeyValue->get($key);
    }

    public function exists(string $key)
    {
        return $this->innerKeyValue->exists($key);
    }

    /**
     * @return KeyValueInterface
     */
    public function getInnerKeyValue()
    {
        return $this->innerKeyValue;
    }
}
