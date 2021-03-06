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
}
