<?php


namespace App\Support;

use Log;

class Processor
{
    const NGRAM_SIZE = 2;

    public function log(string $logMessage, $tag = "REPORT ")
    {
        Log::info("{$tag} {$logMessage}");
    }

    public function textToFingerprintHashes(string $input_text)
    {
        $this->log("Kalimat awal: {$input_text}.");

        $normalizedText = trim(mb_strtolower($input_text));

        $this->log("Setelah case folding: {$normalizedText}.");

        if (!$this->passesTextFilter($normalizedText)) {
            $this->log("Tidak lulus filter: Kalimat panjangnya nol atau dimulai dengan angka.");
            return [];
        }

        $words = $this->tokenize($normalizedText);
        
        $this->log("Setelah tokenisasi: " . collect($words)->join(", "));

        if (!$this->passesWordCountFilter($words)) {
            $this->log("Tidak lulus filter: Jumlah kata terlalu sedikit (< 5).");
            return [];
        }

        $ngrams = ngrams($words, self::NGRAM_SIZE, ' ');

        $this->log("Ngram: " . collect($ngrams)->join(", "));

        $hashes = array_map(fn($token) => hash("adler32", $token), $ngrams);

        $this->log("Hash: " . collect($hashes)->join(", "));

        $fingerprints = $this->extractFingerprint($hashes, self::NGRAM_SIZE);

        $this->log("Fingerprint: " . collect($fingerprints)->join(", "));

        return $fingerprints;
    }

    private function passesTextFilter(string $normalizedText)
    {
        if (mb_strlen($normalizedText) === 0) {
            return false;
        }

        if ($this->textStartsWithNumber($normalizedText)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $normalizedText
     * @return bool
     */
    private function textStartsWithNumber(string $normalizedText): bool
    {
        return preg_match("/^\p{N}/ui", $normalizedText) === 1;
    }

    /** @return array | string[] */
    public function tokenize(string $text): array
    {
        return array_values(array_filter(
            explode(' ', $text),
            fn(string $text) => mb_strlen($text) > 0
        ));
    }

    private function passesWordCountFilter(array $words): bool
    {
        return count($words) > 5;
    }

    public function extractFingerprint(array $hashes, $k, $threshold = 3): array
    {
        $windowLen = $threshold - $k + 1;

        $hashesCount = count($hashes);

        $minValues = [];

        for ($i = 0; $i < $hashesCount - $windowLen + 1; ++$i) {
            $window = array_slice($hashes, $i, $windowLen);
            $minValues[$i + $this->indexOfSmallestRight($window)] = min($window);
        }

        return $minValues;
    }

    private function indexOfSmallestRight(array $hashes)
    {
        return count($hashes) - array_search(
                min($hashes),
                array_reverse($hashes),
                true,
            ) - 1;
    }
}
