<?php

declare(strict_types=1);

namespace Lit\Nexus\Utilities;

use Lit\Nexus\Interfaces\SingleValueInterface;

/**
 * Utilities about KeyValueInterface
 */
class KeyValueUtility
{
    /**
     * Get the value from SingleValueInterface, or call $compute callback and write to it.
     *
     * @param SingleValueInterface $store   The storage.
     * @param callable             $compute The method to create the value.
     * @return mixed The value.
     */
    public static function getOrSet(SingleValueInterface $store, callable $compute)
    {
        if ($store->exists()) {
            return $store->get();
        }
        $value = call_user_func($compute);
        $store->set($value);

        return $value;
    }
}
