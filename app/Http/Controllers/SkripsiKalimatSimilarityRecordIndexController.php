<?php

namespace App\Http\Controllers;

use App\Models\KalimatSkripsi;
use App\Models\Skripsi;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SkripsiKalimatSimilarityRecordIndexController extends Controller
{
    private ResponseFactory $responseFactory;

    public function __construct(ResponseFactory $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(Skripsi $skripsi)
    {
        return $this->responseFactory->json(
            $this->getData($skripsi)
        );
    }

    private function getData(Skripsi $skripsi): Collection
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
            ->where("kalimat_a.skripsi_id", "=", $skripsi->getKey())
            ->where("kalimat_b.skripsi_id", "<>", $skripsi->getKey())
            ->orderByDesc(DB::raw("smlar(kalimat_a.hashes, kalimat_b.hashes, '2 * N.i / (N.a + N.b)')"))
            ->whereRaw("smlar(kalimat_a.hashes, kalimat_b.hashes, '2 * N.i / (N.a + N.b)') > ?", [0.4])
            ->take(25)
            ->get();
    }
}