<?php

namespace Database\Seeders;

use App\Models\BlacklistKalimat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlacklistKalimatSeeder extends Seeder
{
    public array $sentences = [
        "tabel",
        "proses",
        "gambar",
        "bab",
        "daftar pustaka",
        "demikian pernyataan ini dibuat dengan sebenar-benarnya",
        "shalawat dan salam saya panjatkan kepada",
        "puji dan syukur saya panjatkan kepada",
        "sepanjang pengetahuan saya",
        "saya sanggup menerima konsekuensi akademis",
        "yang bertanda tangan di bawah ini",
        "menyatakan bahwa dalam skripsi yang berjudul",
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        BlacklistKalimat::query()
            ->insert(
                array_map(fn($sentence) => ["teks" => $sentence], $this->sentences)
            );

        DB::commit();
    }
}
