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




        $this->getSkripsiSimilarityRecords($targetSkripsi);














        return $this->responseFactory->view("mahasiswa.dashboard", [
            "mahasiswa" => $mahasiswa,
            "mahasiswas" => [],
            "targetSkripsi" => $targetSkripsi,
            "skripsiSimilarityRecords" => $targetSkripsi ? $this->getSkripsiSimilarityRecords($targetSkripsi) : collect(),
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
            ->from((new KalimatSkripsi)->getTable() . " AS kalimat_a")
            ->crossJoin((new KalimatSkripsi)->getTable() . " AS kalimat_b")
            ->where("kalimat_a.skripsi_id", $targetSkripsi->getKey())
            ->where("kalimat_b.skripsi_id", "<>", $targetSkripsi->getKey())
            ->groupBy("kalimat_b.skripsi_id")
            ->orderByDesc(DB::raw("AVG(smlar(kalimat_a.hashes, kalimat_b.hashes, '2 * N.i / (N.a + N.b)'))"))
            ->paginate(5);

        $paginator->getCollection()->transform(function (KalimatSkripsi $kalimatSkripsi) {
            return new SkripsiSimilarityRecord([
                "skripsi" => $kalimatSkripsi->skripsi,
                "avgDiceSimilarity" => $kalimatSkripsi->similaritas,
                "avgChebyshevDistance" => null,
            ]);
        });

        return $paginator;
    }
}
