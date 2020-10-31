<?php


namespace App\Support;


class RollingHash
{
    public static function make(string $string, int $base = 256, int $modulo = 100_003)
    {
        return md5($string);

//        $result = (ord($string[0]) * $base) % $modulo;
//
//        for ($i = 1; $i < strlen($string); ++$i) {
//            $result = ($result + ord($string[$i])) % $modulo;
//        }
//
//        return $result;
    }
}
