<?php

namespace Database\Seeders;

use App\Models\BlacklistKalimat;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminUserSeeder::class);
        $this->call(MahasiswaSeeder::class);
        $this->call(SkripsiSeeder::class);
        $this->call(BlacklistKalimatSeeder::class);
    }
}
