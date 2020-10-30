<?php

namespace App\Models;

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

    public function fingerprint_hashes(): HasMany
    {
        return $this->hasMany(SkripsiFingerprintHash::class);
    }
}
