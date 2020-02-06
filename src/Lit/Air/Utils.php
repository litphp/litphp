<?php

declare(strict_types=1);

namespace Lit\Air;

class Utils
{
    /**
     * Test if a value is a sequential array.
     *
     * @param mixed    $val            The tested value.
     * @param int|null $expectedLength If non null, only return true if the array length is exactly this value.
     * @return bool Whether the tested value is a sequential array.
     */
    public static function isSequentialArray($val, ?int $expectedLength = null): bool
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

        $idx = 0;
        foreach ($val as $k => $v) {
            if ($k !== $idx++) {
                return false;
            }
        }

        return true;
    }
}
