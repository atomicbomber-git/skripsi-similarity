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
            ->count(100)
            ->make()
            ->each(function (User $user, $index) {
                User::insertOrIgnore(array_merge(
                    $user->toArray(),
                    [
                        "username" => "mahasiswa_{$index}",
                        "password" => '$2y$10$Ym4QFCA0FPZFdkgQ0vMnVe6dLt3S4YxISdY1HEf.pSKAUho4W1W8O',
                    ]
                ));
            });

        DB::commit();
    }
}
