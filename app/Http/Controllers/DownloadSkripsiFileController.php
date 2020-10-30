<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DownloadSkripsiFileController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, User $mahasiswa)
    {
        if ($mahasiswa->skripsi === null) {
            return response(null, 404);
        }

        return $mahasiswa->skripsi->getFirstMediaPath();
    }
}
