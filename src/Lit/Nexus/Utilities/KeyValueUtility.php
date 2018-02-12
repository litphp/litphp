<?php namespace Lit\Nexus\Utilities;

use Lit\Nexus\Interfaces\SingleValueInterface;

class KeyValueUtility
{
    /**
     * @param SingleValueInterface $store
     * @param callable $compute
     * @return mixed
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
