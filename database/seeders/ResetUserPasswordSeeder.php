<?php

namespace Database\Seeders;

use App\Models\User;
use Hash;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResetUserPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        User::query()
            ->get(["id", "username"])
            ->each(function (User $user) {
                $user->update([
                    "password" => Hash::make($user->username)
                ]);
            });

        DB::commit();
    }
}
