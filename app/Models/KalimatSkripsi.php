<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KalimatSkripsi extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = "kalimat_skripsi";

    public function skripsi(): BelongsTo
    {
        return $this->belongsTo(Skripsi::class);
    }

    public function kalimatHashes(): HasMany
    {
        return $this->hasMany(KalimatHash::class);
    }

    public function diceSimilarityWith(KalimatSkripsi $anotherKalimat): float
    {
        $aHashes = $this->kalimatHashes->pluck("hash")->unique();
        $bHashes = $anotherKalimat->kalimatHashes->pluck("hash")->unique();

        return
            (2 * $aHashes->intersect($bHashes)->count()) / ($aHashes->count() + $bHashes->count());
    }

    public function chebyshevDistanceFrom(KalimatSkripsi $anotherKalimat): float
    {
        $aHashes = $this->kalimatHashes->pluck("hash")->unique();
        $bHashes = $anotherKalimat->kalimatHashes->pluck("hash")->unique();

        $allHashes = collect()->merge($aHashes)->merge($bHashes);
        $aCounts = $aHashes->countBy();
        $bCounts = $bHashes->countBy();

        $max = 0;
        foreach ($allHashes as $hash) {
            $diff = abs(($aCounts[$hash] ?? 0) - ($bCounts[$hash] ?? 0));
            if ($diff > $max) $max = $diff;
        }

        return $diff;
    }
}
