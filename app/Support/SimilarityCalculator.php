<?php


namespace App\Support;


use Illuminate\Support\Collection;

class SimilarityCalculator
{
    public static function sorensenDiceDistance($textA, $textB)
    {
        $processor = new Processor();
        $hashesA = new Collection($processor->textToFingerprintHashes($textA));
        $hashesB = new Collection($processor->textToFingerprintHashes($textB));

        return (2 * $hashesA->intersect($hashesB)->count()) / ($hashesA->count() + $hashesB->count());
    }
}
