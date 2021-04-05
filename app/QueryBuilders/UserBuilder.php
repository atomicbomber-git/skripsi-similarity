<?php


namespace App\QueryBuilders;


use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Kirschbaum\PowerJoins\PowerJoins;

/** @mixin PowerJoins */
class UserBuilder extends Builder
{
    public function levelIsMahasiswa(): self
    {
        return $this->where("level", User::LEVEL_MAHASISWA);
    }
}