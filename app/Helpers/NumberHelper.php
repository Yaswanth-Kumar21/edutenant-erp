<?php

namespace App\Helpers;

/**
 * NumberHelper
 *
 * Reusable number formatting utilities.
 */
class NumberHelper
{
    public static function currency(float $amount, int $decimals = 0): string
    {
        return '₹' . number_format($amount, $decimals);
    }

    public static function percent(float $value, int $decimals = 1): string
    {
        return number_format($value, $decimals) . '%';
    }

    public static function zeroPad(int $number, int $length = 5): string
    {
        return str_pad($number, $length, '0', STR_PAD_LEFT);
    }
}
