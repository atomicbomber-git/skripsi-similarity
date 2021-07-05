<?php

namespace App\Http\Controllers;

use App\Models\Skripsi;
use App\Models\User;
use App\Providers\AuthServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;

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
        ]);
    }
}
