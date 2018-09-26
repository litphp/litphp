<?php

declare(strict_types=1);

namespace Lit\Nexus\Traits;

use Lit\Nexus\Derived\FrozenKeyValue;
use Lit\Nexus\Derived\PrefixKeyValue;
use Lit\Nexus\Derived\SlicedValue;
use Lit\Nexus\Interfaces\KeyValueInterface;

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
     * @param $key
     * @return SlicedValue
     */
    public function slice($key)
    {
        /**
         * @var KeyValueInterface $this
         */
        return SlicedValue::slice($this, $key);
    }

    /**
     * @param string $prefix
     * @param string $delimeter
     * @return PrefixKeyValue
     */
    public function prefix(string $prefix, string $delimeter = '!!')
    {
        /**
         * @var KeyValueInterface|self $this
         */
        assert($this instanceof KeyValueInterface);
        return PrefixKeyValue::wrap($this, $prefix . $delimeter);
    }
}
