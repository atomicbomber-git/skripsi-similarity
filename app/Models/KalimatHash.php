<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KalimatHash extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = "kalimat_hash";

    public function kalimatSkripsi(): BelongsTo
    {
        return $this->belongsTo(KalimatSkripsi::class);
    }
}
