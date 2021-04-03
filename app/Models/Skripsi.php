<?php

namespace App\Models;

use App\Support\Processor;
use App\Support\Sentence;
use App\Support\Tokenizer;
use DOMDocument;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Skripsi extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $table = "skripsi";
    protected $guarded = [];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function kalimatSkripsis(): HasMany
    {
        return $this->hasMany(KalimatSkripsi::class);
    }

    public function saveKalimatsAndHashesFromDocument(): void
    {
        $sentences = collect();
        foreach ($this->getDomDocuments() as $domDocument) {
            $tokenizer = new Tokenizer();
            $tokenizer->load($domDocument);
            $sentences->push(
                ...array_map(
                    fn (Sentence $sentence) => $sentence->value,
                    $tokenizer->tokenize()
                )
            );
        }

        $processor = new Processor();

        $sentenceAndHashes = $sentences
            ->filter(fn($sentence) => mb_strlen($sentence) > 0)
            ->map(fn($sentence) => [
                "text" => $sentence,
                "hashes" => $processor->textToFingerprintHashes($sentence)
            ])
            ->filter(fn($sentenceAndHash) => $sentenceAndHash["hashes"] !== []);


        $sentenceAndHashes->each(function (array $sentenceAndHash) {
            $kalimatSkripsi = $this->kalimatSkripsis()->create([
                "teks" => $sentenceAndHash["text"],
                "hashes" => "{" . join(",", $sentenceAndHash["hashes"]) . "}",
            ]);

//            KalimatHash::query()->insert(
//                array_map(
//                    fn($hash, $position) => [
//                        "kalimat_skripsi_id" => $kalimatSkripsi->getKey(),
//                        "position" => $position,
//                        "hash" => $hash,
//                        "created_at" => now(),
//                        "updated_at" => now(),
//                    ],
//                    $sentenceAndHash["hashes"],
//                    array_keys($sentenceAndHash["hashes"])
//                )
//            );
        });
    }

    /** @return array | DOMDocument[] */
    public function getDomDocuments(): array
    {
        return $this
            ->media
            ->map(fn (Media $media) => $media->getPath())
            ->map(function (string $mediaPath) {

                $zipArchive = new \ZipArchive();
                $zipResource = $zipArchive->open($mediaPath);

                $domDocument = new \DOMDocument();
                if ($zipResource === true) {
                    $domDocument->loadXML($zipArchive->getFromName("word/document.xml"));
                    $zipArchive->close();
                } else {
                    throw new \Exception("Failed to open zip file.");
                }

                return $domDocument;
            })->toArray();
    }

    public function fingerprint_hashes(): HasMany
    {
        return $this->hasMany(SkripsiFingerprintHash::class);
    }
}
