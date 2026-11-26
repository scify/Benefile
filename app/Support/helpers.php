<?php

/*
 * Helpers for code written for PHP 7.
 */

if (! function_exists('legacy_count')) {
    /**
     * count() as in PHP 7: 0 for null, 1 for any other value that is not countable.
     *
     * The code counts session and request values that can be missing. PHP 8
     * throws an error on count(null); PHP 7 returned 0.
     */
    function legacy_count(mixed $value): int
    {
        if ($value === null) {
            return 0;
        }

        return is_countable($value) ? count($value) : 1;
    }
}
