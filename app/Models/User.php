<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property Skripsi skripsi
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    const LEVEL_ADMIN = "admin";
    const LEVEL_MAHASISWA = "mahasiswa";

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function skripsi(): HasOne
    {
        return $this->hasOne(Skripsi::class);
    }
}
