<?php

namespace Database\Seeders;

use App\Models\Skripsi;
use App\Models\SkripsiFingerprintHash;
use App\Models\User;
use App\Support\Processor;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $dataPath = database_path("seeders/skripsis");

        if (!is_dir($dataPath)) {
            throw new Exception("The directory \"$dataPath\" does not exist.");
        }

        $filemap = [];

        foreach (scandir($dataPath) ?: [] as $file) {
            if (in_array($file, [".", ".."])) {
                continue;
            }

            $info = pathinfo($dataPath . DIRECTORY_SEPARATOR . $file);
            $filename = $info["filename"];
            $filenameParts = explode(" ", $filename);
            $nim = $filenameParts[0];
            $name = join(" ", array_slice($filenameParts, 1));

            $filemap[$nim]["name"] = $this->cleanName($name);

            if ($info["extension"] === "pdf") {
                $filemap[$nim]["pdf"] = $info["dirname"] . DIRECTORY_SEPARATOR . $info["basename"];
            } elseif ($info["extension"] === "txt") {
                $filemap[$nim]["txt"] = $info["dirname"] . DIRECTORY_SEPARATOR . $info["basename"];
            }
        }

        DB::beginTransaction();

        foreach ($filemap as $nim => $data) {
            /** @var User $user */
            $user = User::query()->create([
                "name" => $data["name"],
                "username" => $nim,
                "password" => Hash::make($nim),
                "level" => User::LEVEL_MAHASISWA,
            ]);

            /** @var Skripsi $skripsi */
            $skripsi = $user->skripsi()->create([
                "judul" => "Skripsi {$user->name}",
                "terverifikasi" => 1,
            ]);

            $skripsi->addMedia($data["pdf"])
                ->preservingOriginal()
                ->toMediaCollection();

            $processor = new Processor();

            $this->command->info("Processing {$skripsi->judul}...");

            $start = microtime(true);
            $fingerprintHashes = $processor->wordsToHashes(
                file_get_contents($data["txt"])
            );


            $hashesData = array_map(
                function ($position, $hash) use ($skripsi) {
                    return [
                        "skripsi_id" => $skripsi->id,
                        "position" => $position,
                        "hash" => $hash,
                    ];
                },
                array_keys($fingerprintHashes), $fingerprintHashes,
            );

            foreach (array_chunk($hashesData, 40) as $chunk) {
                DB::table((new SkripsiFingerprintHash())->getTable())
                    ->insert($chunk);
            }

            $elapsed = microtime(true) - $start;
            $this->command->info("Finished processing {$skripsi->judul}. It took {$elapsed} seconds.");

        }


        DB::commit();
    }

    public function cleanName($name)
    {
        foreach (["SKRIPSI", "TA"] as $toBeRemoved) {
            $name = str_ireplace($toBeRemoved, "", $name);
            $name = trim(ucwords(strtolower($name)));
        }

        return $name;
    }
}
