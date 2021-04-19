<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlacklistKalimat extends Model
{
    protected $table = "blacklist_kalimat";
    protected $guarded = [];
    use HasFactory;
}
