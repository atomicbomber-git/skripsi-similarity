<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\KalimatSimilarityRecord;
use App\DataTransferObjects\SkripsiSimilarityRecord;
use App\Models\Skripsi;
use App\Models\SkripsiFingerprintHash;
use App\Models\User;
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

//        WITH kalimats_a AS (
//    SELECT kalimat_skripsi.id, hashes
//    FROM kalimat_skripsi
//             INNER JOIN skripsi s ON s.id = kalimat_skripsi.skripsi_id
//    WHERE user_id = 47
//)
//SELECT b.skripsi_id, SUM(smlar(a.hashes, b.hashes, '2 * N.i / (N.a + N.b)')) FROM kalimats_a a, kalimat_skripsi b
//    WHERE b.id NOT IN (SELECT id FROM kalimats_a)
//    GROUP BY b.skripsi_id

//
//        $otherSkripsis = Skripsi::query()
//            ->with("mahasiswa")
//            ->select("id", "judul", "user_id")
//            ->where("user_id", "<>", $mahasiswa->getKey())
//            ->with([
//                "kalimatSkripsis:id,skripsi_id,teks",
//                "kalimatSkripsis.kalimatHashes:id,kalimat_skripsi_id,hash"
//            ])
//            ->get();
//

        return $this->responseFactory->view("mahasiswa.dashboard", [
            "mahasiswa" => $mahasiswa,
            "mahasiswas" => [],
            "targetSkripsi" => $targetSkripsi,
//            "skripsiSimilarityRecords" => $targetSkripsi ? $this->getSkripsiSimilarityRecords($otherSkripsis, $targetSkripsi) : collect(),
            "skripsiSimilarityRecords" => collect(),
        ]);
    }

    /**
     * @param Collection $otherSkripsis
     * @param Skripsi $targetSkripsi
     * @return Collection | SkripsiSimilarityRecord[]
     */
    public function getSkripsiSimilarityRecords(Collection $otherSkripsis, Skripsi $targetSkripsi): Collection
    {
        return $otherSkripsis->map(function (Skripsi $otherSkripsi) use ($targetSkripsi) {
            $kalimatSimilarities = collect();

            foreach ($targetSkripsi->kalimatSkripsis as $kalimatA) {
                foreach ($otherSkripsi->kalimatSkripsis as $kalimatB) {
                    $kalimatSimilarities->push(new KalimatSimilarityRecord(
                        kalimatAId: $kalimatA->getKey(),
                        kalimatBId: $kalimatB->getKey(),
                        chebyshevDistance: $kalimatA->chebyshevDistanceFrom($kalimatB),
                        diceSimilarity: $kalimatA->diceSimilarityWith($kalimatB),
                    ));
                }
            }

            $maxChebyshev = $kalimatSimilarities->max("chebyshevDistance");

            return new SkripsiSimilarityRecord([
                "skripsi" => $otherSkripsi,
                "mostSimilarKalimats" => $kalimatSimilarities
                    ->sortByDesc(fn(KalimatSimilarityRecord $data) => ($this->divide($data->chebyshevDistance, $maxChebyshev) + $data->diceSimilarity) / 2)
                    ->take(5),
                "chebyshevDistanceAverage" => $kalimatSimilarities->average("chebyshevDistance"),
                "diceSimilarityAverage" => $kalimatSimilarities->average("diceSimilarity"),
            ]);
        });
    }

    public function divide($above, $bottom)
    {
        return $bottom != 0 ?
            $above / $bottom :
            0;
    }
}
