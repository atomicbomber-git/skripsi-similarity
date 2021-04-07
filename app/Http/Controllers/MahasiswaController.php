<?php

namespace App\Http\Controllers;

use App\Constants\MessageState;
use App\Events\SkripsiModified;
use App\Helper\SessionHelper;
use App\Models\User;
use App\Providers\AuthServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class MahasiswaController extends Controller
{
    private ResponseFactory $responseFactory;

    public function __construct(ResponseFactory $responseFactory)
    {
        $this->middleware("auth");
        $this->responseFactory = $responseFactory;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $this->authorize(AuthServiceProvider::CAN_ACCESS_MANAGEMENT_FEATURES);

        return $this->responseFactory->view("mahasiswa.index", [
            "mahasiswas" => User::query()
                ->where("users.level", User::LEVEL_MAHASISWA)
                ->orderByRaw("concat_ws(' ', skripsi.judul, users.name, users.username) <-> ?", [$request->get("search")])
                ->leftJoinRelationship("skripsi")
                ->with("skripsi")
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
        return $this->responseFactory->view("mahasiswa.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            "name" => ["required", "string", "max:100"],
            "username" => ["required", Rule::unique(User::class), "alpha_dash", "max:100"],
            "password" => ["required", "confirmed", "max:100"],
        ]);

        User::query()->create([
            "name" => $data["name"],
            "username" => $data["username"],
            "password" => Hash::make($data["password"]),
            "level" => User::LEVEL_MAHASISWA,
        ]);

        SessionHelper::flashMessage(
            __("messages.create.success"),
            MessageState::STATE_SUCCESS,
        );

        return $this->responseFactory->redirectToRoute("mahasiswa.index");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $mahasiswa
     * @return Response
     */
    public function edit(User $mahasiswa)
    {
        $this->authorize(AuthServiceProvider::CAN_ACCESS_MANAGEMENT_FEATURES);

        return $this->responseFactory->view("mahasiswa.edit", [
            "mahasiswa" => $mahasiswa,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $mahasiswa)
    {
        $this->authorize(AuthServiceProvider::CAN_ACCESS_MANAGEMENT_FEATURES);

        $data = $request->validate([
            "name" => ["required", "string", "max:100"],
            "username" => ["required", Rule::unique(User::class)->ignore($mahasiswa), "alpha_dash", "max:100"],
            "password" => ["nullable", "confirmed", "max:100"]
        ]);

        if (isset($data["password"])) {
            $data["password"] = Hash::make($data["password"]);
        } else {
            unset($data["password"]);
        }

        $mahasiswa->update($data);

        SessionHelper::flashMessage(
            __("messages.update.success"),
            MessageState::STATE_SUCCESS,
        );

        return $this->responseFactory->redirectToRoute("mahasiswa.edit", $mahasiswa);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $mahasiswa
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $mahasiswa)
    {
        $this->authorize(AuthServiceProvider::CAN_ACCESS_MANAGEMENT_FEATURES);

        DB::beginTransaction();

        if ($mahasiswa->skripsi !== null) {
            $mahasiswa->skripsi->media()->delete();
            $mahasiswa->skripsi->kalimatSkripsis()->delete();
            $mahasiswa->skripsi()->delete();

            event(new SkripsiModified);
        }

        $mahasiswa->delete();

        DB::commit();

        SessionHelper::flashMessage(
            __("messages.delete.success"),
            MessageState::STATE_SUCCESS,
        );

        return redirect()->back();
    }
}
