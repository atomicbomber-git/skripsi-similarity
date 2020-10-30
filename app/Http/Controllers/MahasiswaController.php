<?php

namespace App\Http\Controllers;

use App\Constants\MessageState;
use App\Helper\SessionHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\DB;

class MahasiswaController extends Controller
{
    private ResponseFactory $responseFactory;

    public function __construct(ResponseFactory $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return $this->responseFactory->view("mahasiswa.index", [
            "mahasiswas" => User::query()
                ->where("level", User::LEVEL_MAHASISWA)
                ->orderBy("name")
                ->paginate()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $mahasiswa
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $mahasiswa)
    {
        DB::beginTransaction();

        $mahasiswa->skripsi->media()->delete();
        $mahasiswa->skripsi()->delete();
        $mahasiswa->delete();

        DB::commit();

        SessionHelper::flashMessage(
            __("messages.delete.success"),
            MessageState::STATE_SUCCESS,
        );

        return redirect()
            ->back();
    }
}
