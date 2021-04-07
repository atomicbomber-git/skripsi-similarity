<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkripsiHash extends Model
{
    protected $primaryKey = "skripsi_id";
    public $table = "skripsi_hashes";

    public function skripsi(): BelongsTo
    {
        return $this->belongsTo(Skripsi::class);
    }
}