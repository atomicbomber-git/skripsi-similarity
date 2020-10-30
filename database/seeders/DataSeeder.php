<?php

namespace Database\Seeders;

use App\Models\User;
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

            $user->skripsi()->create([
                "judul" => "Skripsi {$user->name}",
                "fingerprint" => null,
                "terverifikasi" => 1,
            ]);
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
