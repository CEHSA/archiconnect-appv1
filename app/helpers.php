<?php

use App\Helpers\CurrencyHelper;

if (!function_exists('format_currency')) {
    /**
     * Format a value as a ZAR currency string.
     *
     * @param float|int|null $value
     * @param int $precision
     * @return string
     */
    function format_currency($value, int $precision = 2): string
    {
        return CurrencyHelper::formatZAR($value, $precision);
    }
}
