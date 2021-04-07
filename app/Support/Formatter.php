<?php


namespace App\Support;


class Formatter
{
    public static function number($value): string
    {
        return is_null($value) ? "-" : number_format($value);
    }

    public static function percentage($value): string
    {
        $formatted = number_format($value * 100, 2);
        $formatted = rtrim($formatted, '0');
        $formatted = rtrim($formatted, '.');

        return is_null($value) ? "-" : $formatted . " %";
    }
}