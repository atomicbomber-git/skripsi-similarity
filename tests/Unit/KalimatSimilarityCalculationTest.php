<?php

namespace Tests\Unit;

use App\Models\KalimatHash;
use App\Models\KalimatSkripsi;
use App\Models\SkripsiFingerprintHash;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\TestCase;

class KalimatSimilarityCalculationTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {

//        KalimatSkripsi::query()
//            ->addSelect([
//                "similarity" => KalimatHash::query()
//                    ->from(DB::raw("kalimat_hash AS hash_a"))
//                    ->selectRaw("
//                            2 * COUNT(DISTINCT hash_a.hash)  
//                            /
//                            (      
//                              (
//                                  SELECT COUNT(DISTINCT sfh1.hash) FROM kalimat_hash sfh1
//                                      JOIN kalimat_skripsi s1 ON s1.id = sfh1.kalimat_skripsi_id
//                                      WHERE s1.user_id = ?
//                              ) +
//            
//                              (
//                                  SELECT COUNT(DISTINCT sfh2.hash) FROM kalimat_hash sfh2
//                                      JOIN kalimat_skripsi s2 ON s2.id = sfh2.kalimat_skripsi_id
//                                      WHERE s2.user_id = users.id            
//                              )
//                            )
//                        ", [$mahasiswa->id]
//                    )
//                    ->whereColumn("hash_a.kalimat_skripsi_id", "skripsi.id")
//                    ->whereIn(
//                        "hash_a.hash",
//                        $userSkripsiHashesQuery
//                    )
//            ])
//            ->selectRaw("
//                GREATEST(
//                    (
//                        SELECT ABS((
//                                       SELECT COUNT(sfh11.hash)
//                                       FROM kalimat_hash sfh11
//                                       WHERE sfh11.hash = sfh1.hash
//                                         AND sfh11.kalimat_skripsi_id = sfh1.kalimat_skripsi_id
//                                   ) -
//                                   (
//                                       SELECT COUNT(sfh11.hash)
//                                       FROM kalimat_hash sfh11
//                                       WHERE sfh11.hash = sfh1.hash
//                                         AND sfh11.kalimat_skripsi_id = ?
//                                   )) AS count
//                        FROM kalimat_hash sfh1
//                        WHERE sfh1.kalimat_skripsi_id = skripsi.id
//                        ORDER BY count DESC
//                        LIMIT 1
//                    )
//                , (
//                        SELECT ABS((
//                                       SELECT COUNT(sfh21.hash)
//                                       FROM kalimat_hash sfh21
//                                       WHERE sfh21.hash = sfh2.hash
//                                         AND sfh21.kalimat_skripsi_id = skripsi.id
//                                   ) -
//                                   (
//                                       SELECT COUNT(sfh22.hash)
//                                       FROM kalimat_hash sfh22
//                                       WHERE sfh22.hash = sfh2.hash
//                                         AND sfh22.kalimat_skripsi_id = ?
//                                   )) AS count
//                        FROM kalimat_hash sfh2
//                        WHERE sfh2.kalimat_skripsi_id = ?
//                        ORDER BY count DESC
//                        LIMIT 1
//                    )
//                ) AS chebyshev_distance
//            ", [$mahasiswa->skripsi->id ?? null, $mahasiswa->skripsi->id ?? null, $mahasiswa->skripsi->id ?? null])
//            ->orderByDesc("similarity")
//            ->paginate();


    }
}
