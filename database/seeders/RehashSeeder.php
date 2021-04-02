<?php

namespace Database\Seeders;

use App\Models\KalimatSkripsi;
use App\Models\Skripsi;
use Illuminate\Database\Seeder;

class RehashSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::beginTransaction();

        Skripsi::query()
            ->with("kalimatSkripsis.kalimatHashes")
            ->get()
            ->each(function (Skripsi $skripsi) {
                $skripsi->kalimatSkripsis->each(function (KalimatSkripsi $kalimatSkripsi) {
                    $kalimatSkripsi->kalimatHashes()->delete();
                });

                $skripsi->kalimatSkripsis()->delete();
                $skripsi->saveKalimatsAndHashesFromDocument();
            });


        \Illuminate\Support\Facades\DB::commit();





    }
}
