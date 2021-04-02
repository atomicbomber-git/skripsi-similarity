<?php


namespace App\Support;

class Processor
{
    const NGRAM_SIZE = 5;

    private function indexOfSmallestRight(array $hashes) {
        return count($hashes) - array_search(
            min($hashes),
            array_reverse($hashes),
            true,
        ) - 1;
    }

    public function extractFingerprint(array $hashes, $k, $threshold = 40): array {
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
        $text = strtolower($input_text);
        $text = preg_replace("/[^A-Za-z]/", "", $text);

        $letters = str_split($text, 1);
        $ngrams = ngrams($letters, self::NGRAM_SIZE, '');

        $hashes = array_map(fn ($token) => hash("adler32", $token), $ngrams);
        return $this->extractFingerprint($hashes, self::NGRAM_SIZE);
    }
}
