<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        UserFactory::new()
            ->mahasiswa()
            ->count(40)
            ->create()
            ->each(function (User $user, $index) {
                $user->update([
                    "username" => "mahasiswa_{$index}",
                    "password" => Hash::make("mahasiswa_{$index}"),
                ]);
            });

        DB::commit();
    }
}
