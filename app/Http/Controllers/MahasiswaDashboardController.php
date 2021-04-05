<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\KalimatSimilarityRecord;
use App\DataTransferObjects\SkripsiSimilarityRecord;
use App\Models\KalimatSkripsi;
use App\Models\Skripsi;
use App\Models\SkripsiFingerprintHash;
use App\Models\User;
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
        $mahasiswa->load("skripsi");

        $targetSkripsi = Skripsi::query()
            ->select("id", "judul", "user_id")
            ->where("user_id", "=", $mahasiswa->getKey())
            ->with([
                "kalimatSkripsis:id,skripsi_id,teks",
                "kalimatSkripsis.kalimatHashes:id,kalimat_skripsi_id,hash"
            ])
            ->first();

        return $this->responseFactory->view("mahasiswa.dashboard", [
            "mahasiswa" => $mahasiswa,
            "mahasiswas" => [],
            "targetSkripsi" => $targetSkripsi,
            "skripsiSimilarityRecords" => $targetSkripsi ? $this->getSkripsiSimilarityRecords($targetSkripsi) : collect(),
            "kalimatSimilarityRecords" => $targetSkripsi ? $this->getKalimatSimilarityRecords($targetSkripsi) : collect(),
        ]);
    }

    public function divide($above, $bottom)
    {
        return $bottom != 0 ?
            $above / $bottom :
            0;
    }

    /**
     * @param Skripsi $targetSkripsi
     * @return Paginator | SkripsiSimilarityRecord[]
     */
    public function getSkripsiSimilarityRecords(Skripsi $targetSkripsi): Paginator
    {
        $paginator = KalimatSkripsi::query()
            ->with("skripsi.mahasiswa")
            ->select("kalimat_b.skripsi_id")
            ->selectRaw("AVG(smlar(kalimat_a.hashes, kalimat_b.hashes, '2 * N.i / (N.a + N.b)')) AS similaritas")
            ->selectRaw(<<<HERE
AVG(
    (WITH
            p AS (SELECT hash, count(hash) AS count FROM unnest(kalimat_a.hashes) AS hash GROUP BY hash),
            q AS (SELECT hash, count(hash) AS count FROM unnest(kalimat_b.hashes) AS hash GROUP BY hash)
        SELECT max(abs(coalesce(p.count, 0) - coalesce(q.count, 0)))  FROM
            p FULL OUTER JOIN q ON p.hash = q.hash)
) AS chebyshev_distance
HERE
)
            ->from((new KalimatSkripsi)->getTable() . " AS kalimat_a")
            ->crossJoin((new KalimatSkripsi)->getTable() . " AS kalimat_b")
            ->where("kalimat_a.skripsi_id", $targetSkripsi->getKey())
            ->where("kalimat_b.skripsi_id", "<>", $targetSkripsi->getKey())
            ->groupBy("kalimat_b.skripsi_id")
            ->orderByDesc(DB::raw("AVG(smlar(kalimat_a.hashes, kalimat_b.hashes, '2 * N.i / (N.a + N.b)'))"))
            ->paginate(10);

        $paginator->getCollection()->transform(function (KalimatSkripsi $kalimatSkripsi) {
            return new SkripsiSimilarityRecord([
                "skripsi" => $kalimatSkripsi->skripsi,
                "avgDiceSimilarity" => $kalimatSkripsi->similaritas,
                "avgChebyshevDistance" => $kalimatSkripsi->chebyshev_distance,
            ]);
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
HERE)
            ->from((new KalimatSkripsi)->getTable() . " AS kalimat_a")
            ->crossJoin((new KalimatSkripsi)->getTable() . " AS kalimat_b")
            ->where("kalimat_a.skripsi_id", "=", $targetSkripsi->getKey())
            ->where("kalimat_b.skripsi_id", "<>", $targetSkripsi->getKey())
            ->orderByDesc(DB::raw("smlar(kalimat_a.hashes, kalimat_b.hashes, '2 * N.i / (N.a + N.b)')"))
            ->take(10)
            ->get();
    }
}
