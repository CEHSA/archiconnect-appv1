<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Format a value as a ZAR currency string.
     *
     * @param float|int|null $value
     * @param int $precision
     * @return string
     */
    public static function formatZAR($value, int $precision = 2): string
    {
        if ($value === null) {
            return 'N/A';
        }
        
        return 'R' . number_format((float) $value, $precision);
    }
}
