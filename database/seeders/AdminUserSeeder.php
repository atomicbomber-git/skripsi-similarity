<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::query()->updateOrCreate([
            "username" => "admin",
            "level" => User::LEVEL_ADMIN,
        ], [
            "name" => "Admin",
            "password" => Hash::make("admin"),
        ]);
    }
}
