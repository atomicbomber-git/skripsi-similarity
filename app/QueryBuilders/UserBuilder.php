<?php


namespace App\QueryBuilders;


use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserBuilder extends Builder
{
    public function levelIsMahasiswa(): self
    {
        return $this->where("level", User::LEVEL_MAHASISWA);
    }
}