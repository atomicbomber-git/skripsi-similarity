<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        /* TODO: Authorization check */

        return $this->responseFactory->view("bank-skripsi-mahasiswa", [
            "mahasiswas" => User::query()
                ->levelIsMahasiswa()
                ->with("skripsi")
                ->orderBy("name")
                ->paginate()
        ]);
    }
}