<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\SkripsiSimilarityRecord;
use App\Models\KalimatSkripsi;
use App\Models\Skripsi;
use App\Models\SkripsiHash;
use App\Models\User;
use App\Providers\AuthServiceProvider;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MahasiswaDashboardController extends Controller
{
    private ResponseFactory $responseFactory;

    public function __construct(ResponseFactory $responseFactory)
    {
        $this->middleware("auth");
        $this->responseFactory = $responseFactory;
    }

    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request, User $mahasiswa)
    {
        $this->authorize(AuthServiceProvider::CAN_ACCESS_MAHASISWA_DASHBOARD);
        $mahasiswa->load("skripsi");

        $targetSkripsi = Skripsi::query()
            ->select("id", "judul", "user_id")
            ->where("user_id", "=", $mahasiswa->getKey())
            ->first();

        return $this->responseFactory->view("mahasiswa.dashboard", [
            "mahasiswa" => $mahasiswa,
            "mahasiswas" => [],
            "targetSkripsi" => $targetSkripsi,
            "skripsiSimilarityRecords" => $targetSkripsi ? $this->getSkripsiSimilarityRecords($targetSkripsi) : collect(),
            "kalimatSimilarityRecords" => $targetSkripsi ? $this->getKalimatSimilarityRecords($targetSkripsi) : collect(),
        ]);
    }

    /**
     * @param Skripsi $targetSkripsi
     * @return Paginator | SkripsiSimilarityRecord[]
     */
    public function getSkripsiSimilarityRecords(Skripsi $targetSkripsi): Paginator
    {
        $paginator = SkripsiHash::query()
            ->select("skripsi_hashes.skripsi_id")
            ->selectRaw("smlar(skripsi_hashes.hashes, other_skripsi_hashes.hashes, '2 * N.i / (N.a + N.b)') AS dice_similarity")
            ->selectRaw(<<<HERE
(WITH
        p AS (SELECT hash, count(hash) AS count FROM unnest(skripsi_hashes.hashes) AS hash GROUP BY hash),
        q AS (SELECT hash, count(hash) AS count FROM unnest(other_skripsi_hashes.hashes) AS hash GROUP BY hash)
    SELECT max(abs(coalesce(p.count, 0) - coalesce(q.count, 0)))  FROM
        p FULL OUTER JOIN q ON p.hash = q.hash) AS chebyshev_distance
HERE
            )
            ->whereKeyNot($targetSkripsi->getKey())
            ->with("skripsi")
            ->crossJoinSub(
                SkripsiHash::query()
                    ->whereKey($targetSkripsi->getKey())
                , "other_skripsi_hashes")
            ->orderByDesc(DB::raw("smlar(skripsi_hashes.hashes, other_skripsi_hashes.hashes, '2 * N.i / (N.a + N.b)')"))
            ->paginate();

        $paginator->getCollection()->transform(function (SkripsiHash $skripsiHash) {
            return new SkripsiSimilarityRecord(
                skripsi: $skripsiHash->skripsi,
                diceSimilarity: $skripsiHash->dice_similarity,
                chebyshevDistance: $skripsiHash->chebyshev_distance,
            );
        });

        return $paginator;
    }

    private function getKalimatSimilarityRecords(Skripsi $targetSkripsi): Collection
    {
        return KalimatSkripsi::query()
            ->with("skripsi.mahasiswa")
            ->select([
                "kalimat_b.skripsi_id",
                "kalimat_a.teks AS teks_a",
                "kalimat_b.teks AS teks_b",
            ])
            ->selectRaw("smlar(kalimat_a.hashes, kalimat_b.hashes, '2 * N.i / (N.a + N.b)') AS similaritas")
            ->selectRaw(<<<HERE
(
SELECT * FROM ((WITH
                   p AS (SELECT hash, count(hash) AS count FROM unnest(kalimat_a.hashes) AS hash GROUP BY hash),
                   q AS (SELECT hash, count(hash) AS count FROM unnest(kalimat_b.hashes) AS hash GROUP BY hash)
               SELECT max(abs(coalesce(p.count, 0) - coalesce(q.count, 0)))  FROM
                   p FULL OUTER JOIN q ON p.hash = q.hash)) AS x
) AS chebyshev_distance
HERE
            )
            ->from((new KalimatSkripsi)->getTable() . " AS kalimat_a")
            ->crossJoin((new KalimatSkripsi)->getTable() . " AS kalimat_b")
            ->whereRaw("kalimat_a.teks NOT IN (SELECT teks FROM blacklist_kalimat)")
            ->whereRaw("kalimat_b.teks NOT IN (SELECT teks FROM blacklist_kalimat)")
            ->where("kalimat_a.skripsi_id", "=", $targetSkripsi->getKey())
            ->where("kalimat_b.skripsi_id", "<>", $targetSkripsi->getKey())
            ->orderByDesc(DB::raw("smlar(kalimat_a.hashes, kalimat_b.hashes, '2 * N.i / (N.a + N.b)')"))
            ->whereRaw("smlar(kalimat_a.hashes, kalimat_b.hashes, '2 * N.i / (N.a + N.b)') > ?", [0.4])
            ->take(25)
            ->get();
    }
}
