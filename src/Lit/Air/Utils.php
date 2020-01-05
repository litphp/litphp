<?php

declare(strict_types=1);

namespace Lit\Air;

class Utils
{
    public static function isSequentialArray($val, int $expectedLength = null): bool
    {
        if (!is_array($val)) {
            return false;
        }

        $cnt = count($val);
        if ($cnt === 0) {
            return $expectedLength === null || $expectedLength === 0;
        }

        // non-empty sequential array must have zero index, fast exit for 99% assoc arrays
        if (!array_key_exists(0, $val)) {
            return false;
        }

        // fast exit for length assertion
        if ($expectedLength !== null && $expectedLength !== $cnt) {
            return false;
        }

        foreach (array_keys($val) as $k => $v) {
            if ($k !== $v) {
                return false;
            }
        }

        return true;
    }
}
