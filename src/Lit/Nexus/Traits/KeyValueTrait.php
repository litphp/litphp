<?php

declare(strict_types=1);

namespace Lit\Nexus\Traits;

use Lit\Nexus\Derived\FrozenKeyValue;
use Lit\Nexus\Derived\PrefixKeyValue;
use Lit\Nexus\Derived\SlicedValue;
use Lit\Nexus\Interfaces\KeyValueInterface;

/**
 * Wrapper methods for KeyValueInterface
 */
trait KeyValueTrait
{
    /**
     * @return FrozenKeyValue
     */
    public function freeze()
    {
        /**
         * @var KeyValueInterface $this
         */
        return FrozenKeyValue::wrap($this);
    }

    /**
     * @param string $key The key.
     * @return SlicedValue
     */
    public function slice(string $key)
    {
        /**
         * @var KeyValueInterface $this
         */
        return SlicedValue::slice($this, $key);
    }

    /**
     * @param string $prefix    The prefix.
     * @param string $delimeter The delimeter.
     * @return PrefixKeyValue
     */
    public function prefix(string $prefix, string $delimeter = '!!')
    {
        assert($this instanceof KeyValueInterface);
        /**
         * @var KeyValueInterface $this
         */
        return PrefixKeyValue::wrap($this, $prefix . $delimeter);
    }
}
