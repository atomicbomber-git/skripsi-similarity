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
        return is_null($value) ? "-" : number_format($value, 4) . " %";
    }
}