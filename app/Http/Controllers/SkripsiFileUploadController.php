<?php

namespace App\Http\Controllers;

use App\Constants\MessageState;
use App\Events\SkripsiModified;
use App\Helper\SessionHelper;
use App\Models\Skripsi;
use App\Models\User;
use App\Providers\AuthServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\DB;

class SkripsiFileUploadController extends Controller
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
     * @param User $mahasiswa
     * @return RedirectResponse
     */
    public function __invoke(Request $request, User $mahasiswa)
    {
        $this->authorize(AuthServiceProvider::CAN_ACCESS_MAHASISWA_DASHBOARD);

        $data = $request->validate([
            "judul" => ["required", "string", "max:10000"],
            "skripsis.*" => ["file", "mimetypes:application/vnd.openxmlformats-officedocument.wordprocessingml.document"],
        ]);

        DB::beginTransaction();

        /** @var Skripsi $skripsi */
        $skripsi = $mahasiswa->skripsi()->create([
            "judul" => $data["judul"]
        ]);

        foreach ($request->file("skripsis") as $uploadedFile) {
            $skripsi
                ->addMediaFromString(file_get_contents($uploadedFile->getRealPath()))
                ->usingFileName($uploadedFile->getClientOriginalName())
                ->toMediaCollection();
        }

        $skripsi->saveKalimatsAndHashesFromDocument();
        event(new SkripsiModified);

        DB::commit();

        SessionHelper::flashMessage(
            __("messages.create.success"),
            MessageState::STATE_SUCCESS,
        );

        return $this->responseFactory->redirectTo(route("mahasiswa.dashboard"));
    }
}
