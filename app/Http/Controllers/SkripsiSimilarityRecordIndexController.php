<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\SkripsiSimilarityRecord;
use App\Models\Skripsi;
use App\Models\SkripsiHash;
use Illuminate\Support\Facades\DB;

class SkripsiSimilarityRecordIndexController extends Controller
{
    public function __invoke(Skripsi $skripsi)
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
            ->whereKeyNot($skripsi->getKey())
            ->with("skripsi")
            ->crossJoinSub(
                SkripsiHash::query()
                    ->whereKey($skripsi->getKey())
                    ->getQuery()
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
}