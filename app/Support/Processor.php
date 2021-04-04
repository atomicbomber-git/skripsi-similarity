<?php


namespace App\Support;

class Processor
{
    const NGRAM_SIZE = 3;

    private function indexOfSmallestRight(array $hashes) {
        return count($hashes) - array_search(
            min($hashes),
            array_reverse($hashes),
            true,
        ) - 1;
    }

    public function extractFingerprint(array $hashes, $k, $threshold = 5): array {
        $windowLen = $threshold - $k + 1;

        $hashesCount = count($hashes);

        $minValues = [];

        for ($i = 0; $i < $hashesCount - $windowLen + 1; ++$i) {
            $window = array_slice($hashes, $i, $windowLen);
            $minValues[$i + $this->indexOfSmallestRight($window)] = min($window);
        }

        return $minValues;
    }

    public function textToFingerprintHashes(string $input_text)
    {
        $text = mb_strtolower($input_text);

        $words = explode(" ", $text);

        $ngrams = ngrams($words, self::NGRAM_SIZE, ' ');

        $hashes = array_map(fn ($token) => hash("adler32", $token), $ngrams);
        return $this->extractFingerprint($hashes, self::NGRAM_SIZE);
    }
}
