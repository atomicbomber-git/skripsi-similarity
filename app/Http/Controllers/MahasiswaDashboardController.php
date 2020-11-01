<?php

namespace App\Http\Controllers;

use App\Models\SkripsiFingerprintHash;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\DB;

class MahasiswaDashboardController extends Controller
{
    private ResponseFactory $responseFactory;

    public function __construct(ResponseFactory $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, User $mahasiswa)
    {
        $mahasiswa->load("skripsi");

        $userSkripsiHashesQuery =
            SkripsiFingerprintHash::query()
                ->select("hash_b.hash")
                ->from(DB::raw("skripsi_fingerprint_hash AS hash_b"))
                ->join(DB::raw("skripsi skripsi_b"), "skripsi_b.id", "=", "hash_b.skripsi_id")
                ->where("skripsi_b.user_id", "=", $mahasiswa->id);

        $mahasiswas = User::query()
            ->select("users.id", "users.name", "skripsi.judul AS judul")
            ->where("level", User::LEVEL_MAHASISWA)
            ->where("users.id", "<>", $mahasiswa->id)
            ->leftJoin("skripsi", "skripsi.user_id", "=", "users.id")
            ->addSelect([
                "similarity" => SkripsiFingerprintHash::query()
                    ->from(DB::raw("skripsi_fingerprint_hash AS hash_a"))
                    ->selectRaw("
                2 * COUNT(DISTINCT hash_a.hash)  
                /
                (      
                  (
                      SELECT COUNT(DISTINCT sfh1.hash) FROM skripsi_fingerprint_hash sfh1
                          JOIN skripsi s1 ON s1.id = sfh1.skripsi_id
                          WHERE s1.user_id = ?
                  ) +

                  (
                      SELECT COUNT(DISTINCT sfh2.hash) FROM skripsi_fingerprint_hash sfh2
                          JOIN skripsi s2 ON s2.id = sfh2.skripsi_id
                          WHERE s2.user_id = users.id            
                  )
                )
            ", [$mahasiswa->id])
                    ->whereColumn("hash_a.skripsi_id", "skripsi.id")
                    ->whereIn(
                        "hash_a.hash",
                        $userSkripsiHashesQuery
                    )
            ])
            ->selectRaw("
                GREATEST(
                    (
                        SELECT ABS((
                                       SELECT COUNT(sfh11.hash)
                                       FROM skripsi_fingerprint_hash sfh11
                                       WHERE sfh11.hash = sfh1.hash
                                         AND sfh11.skripsi_id = sfh1.skripsi_id
                                   ) -
                                   (
                                       SELECT COUNT(sfh11.hash)
                                       FROM skripsi_fingerprint_hash sfh11
                                       WHERE sfh11.hash = sfh1.hash
                                         AND sfh11.skripsi_id = 2
                                   )) AS count
                        FROM skripsi_fingerprint_hash sfh1
                        WHERE sfh1.skripsi_id = skripsi.id
                        ORDER BY count DESC
                        LIMIT 1
                    )
                , (
                        SELECT ABS((
                                       SELECT COUNT(sfh21.hash)
                                       FROM skripsi_fingerprint_hash sfh21
                                       WHERE sfh21.hash = sfh2.hash
                                         AND sfh21.skripsi_id = skripsi.id
                                   ) -
                                   (
                                       SELECT COUNT(sfh22.hash)
                                       FROM skripsi_fingerprint_hash sfh22
                                       WHERE sfh22.hash = sfh2.hash
                                         AND sfh22.skripsi_id = 2
                                   )) AS count
                        FROM skripsi_fingerprint_hash sfh2
                        WHERE sfh2.skripsi_id = 2
                        ORDER BY count DESC
                        LIMIT 1
                    )
                ) AS chebyshev_distance
            ")
            ->orderByDesc("similarity")
            ->paginate();

        return $this->responseFactory->view("mahasiswa.dashboard", [
            "mahasiswa" => $mahasiswa,
            "mahasiswas" => $mahasiswas,
        ]);
    }
}
