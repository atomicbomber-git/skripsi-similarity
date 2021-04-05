<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Providers\AuthServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;

class BankSkripsiMahasiswaController extends Controller
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
    public function __invoke(Request $request)
    {
        $this->authorize(AuthServiceProvider::CAN_ACCESS_BANK_SKRIPSI_MAHASISWA);

        return $this->responseFactory->view("bank-skripsi-mahasiswa", [
            "mahasiswas" => User::query()
                ->levelIsMahasiswa()
                ->leftJoinRelationship("skripsi")
                ->orderByRaw("concat_ws(' ', skripsi.judul, users.name) <-> ?", [$request->get("search")])
                ->with("skripsi")
                ->orderBy("name")
                ->paginate()
        ]);
    }
}
