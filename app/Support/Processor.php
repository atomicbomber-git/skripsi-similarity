<?php


namespace App\Support;

use Illuminate\Support\Str;

class Processor
{
    const NGRAM_SIZE = 2;

    const FORBIDDEN_WORD_STARTS = [
        "tabel",
        "proses",
        "gambar",
        "bab",
        "daftar pustaka",
    ];

    private function indexOfSmallestRight(array $hashes) {
        return count($hashes) - array_search(
            min($hashes),
            array_reverse($hashes),
            true,
        ) - 1;
    }

    public function extractFingerprint(array $hashes, $k, $threshold = 3): array {
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
        $normalizedText = trim(mb_strtolower($input_text));

        if (! $this->passesTextFilter($normalizedText)) {
            return [];
        }

        $words = $this->tokenize($normalizedText);

        if (! $this->passesWordCountFilter($words)) {
            return [];
        }

        $ngrams = ngrams($words, self::NGRAM_SIZE, ' ');

        $hashes = array_map(fn ($token) => hash("adler32", $token), $ngrams);

        return $this->extractFingerprint($hashes, self::NGRAM_SIZE);
    }

    /** @return array | string[] */
    public function tokenize(string $text): array
    {
        return array_values(array_filter(
            explode(' ', $text),
            fn (string $text) => mb_strlen($text) > 0
        ));
    }

    private function passesTextFilter(string $normalizedText)
    {
        if (mb_strlen($normalizedText) === 0) {
            return false;
        }

        if ($this->textStartsWithNumber($normalizedText)) {
            return false;
        }

        if (Str::startsWith($normalizedText, self::FORBIDDEN_WORD_STARTS)) {
            return false;
        }

        return true;
    }

    private function passesWordCountFilter(array $words): bool
    {
        return count($words) > 5;
    }

    /**
     * @param string $normalizedText
     * @return bool
     */
    private function textStartsWithNumber(string $normalizedText): bool
    {
        return preg_match("/^\p{N}/ui", $normalizedText) === 1;
    }
}
