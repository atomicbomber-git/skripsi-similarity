<?php

namespace Database\Seeders;

use App\Events\SkripsiModified;
use App\Models\KalimatHash;
use App\Models\KalimatSkripsi;
use App\Models\Skripsi;
use App\Models\User;
use Hash;
use Illuminate\Database\Seeder;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class SkripsiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        KalimatHash::query()->delete();
        KalimatSkripsi::query()->delete();
        Skripsi::query()->delete();
        User::query()->delete();

        $rootDirectory = __DIR__ . "/skripsi_docxs";

        foreach (scandir($rootDirectory) as $directoryName) {
            if (in_array($directoryName, [".", ".."])) continue;

            $filenameParts = explode(' ', $directoryName);

            $nim = array_shift($filenameParts);
            $name = join(' ', $filenameParts);

            /** @var User $user */
            $user = User::factory()
                ->mahasiswa()
                ->create([
                    "name" => $name,
                    "username" => $nim,
                    "password" => Hash::make($nim)
                ]);

            $skripsi = $user->skripsi()->create([
                "judul" => "Skripsi {$name} ({$nim})"
            ]);

            /** @var RecursiveDirectoryIterator | SplFileInfo[] $iterator */
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(
                $rootDirectory . "/" . $directoryName
            ));

            foreach ($iterator as $file) {
                if ($file->getExtension() === "docx") {
                    $skripsi
                        ->addMediaFromString(file_get_contents($file->getRealPath()))
                        ->usingFileName($file->getFilename())
                        ->preservingOriginal()
                        ->toMediaCollection();

                    $skripsi->saveKalimatsAndHashesFromDocument();
                }
            }
        }

        event(new SkripsiModified());
    }
}
