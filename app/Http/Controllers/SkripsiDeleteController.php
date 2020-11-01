<?php

namespace App\Http\Controllers;

use App\Constants\MessageState;
use App\Helper\SessionHelper;
use App\Models\SkripsiFingerprintHash;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SkripsiDeleteController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(Request $request, User $mahasiswa)
    {
        DB::beginTransaction();

        $mahasiswa->skripsi->fingerprint_hashes()->delete();
        $mahasiswa->skripsi()->delete();

        DB::commit();

        SessionHelper::flashMessage(
            __("messages.delete.success"),
            MessageState::STATE_SUCCESS,
        );

        return redirect()->back();
    }
}
