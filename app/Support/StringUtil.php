<?php


namespace App\Support;


class StringUtil
{
    public static function trimAndLowercaseUnicode(string $text): string
    {
        return mb_strtolower(static::trimUnicode($text));
    }

    public static function trimUnicode(string $text): string
    {
        $temp = $text;
        $temp = preg_replace("/^[\p{P}\p{S}\p{Z}\p{C}]*/u", "", $temp);
        $temp = preg_replace("/[\p{P}\p{S}\p{Z}\p{C}]*$/u", "", $temp);
        return $temp;
    }
}