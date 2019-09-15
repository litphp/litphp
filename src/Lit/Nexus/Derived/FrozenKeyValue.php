<?php

declare(strict_types=1);

namespace Lit\Nexus\Derived;

use Lit\Nexus\Interfaces\KeyValueInterface;
use Lit\Nexus\Interfaces\ReadableKeyValueInterface;

/**
 * Derived ReadableKeyValueInterface class from a KeyValueInterface. Makes it readonly.
 */
class FrozenKeyValue implements ReadableKeyValueInterface
{
    /**
     * @var KeyValueInterface
     */
    protected $keyValue;

    protected function __construct(KeyValueInterface $keyValue)
    {
        $this->keyValue = $keyValue;
    }


    /**
     * Return a readonly instance of the given KeyValueInterface
     *
     * @param KeyValueInterface $keyValue The KeyValueInterface object.
     * @return static
     */
    public static function wrap(KeyValueInterface $keyValue)
    {
        if ($keyValue instanceof static) {
            return $keyValue;
        }
        return new static($keyValue);
    }

    /**
     * Wrap a offset content.
     *
     * @param array|\ArrayAccess $content The content.
     * @return static
     */
    public static function wrapOffset($content)
    {
        return self::wrap(OffsetKeyValue::wrap($content));
    }

    public function get(string $key)
    {
        return $this->keyValue->get($key);
    }

    public function exists(string $key)
    {
        return $this->keyValue->exists($key);
    }
}
