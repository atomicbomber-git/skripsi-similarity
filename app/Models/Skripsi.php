<?php

namespace App\Models;

use DOMDocument;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Skripsi extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $table = "skripsi";
    protected $guarded = [];

    public function kalimatSkripsis(): HasMany
    {
        return $this->hasMany(KalimatSkripsi::class);
    }

    public function getDomDocument(): DOMDocument
    {
        $zipArchive = new \ZipArchive();
        $zipResource = $zipArchive->open($this->getFirstMediaPath());

        $domDocument = new \DOMDocument();
        if ($zipResource === true) {
            $domDocument->loadXML($zipArchive->getFromName("word/document.xml"));
            $zipArchive->close();
        } else {
            throw new \Exception("Failed to open zip file.");
        }

        return $domDocument;
    }

    public function fingerprint_hashes(): HasMany
    {
        return $this->hasMany(SkripsiFingerprintHash::class);
    }
}
