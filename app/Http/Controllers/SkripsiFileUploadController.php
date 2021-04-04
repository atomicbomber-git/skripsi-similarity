<?php

namespace App\Http\Controllers;

use App\Constants\MessageState;
use App\Helper\SessionHelper;
use App\Models\KalimatHash;
use App\Models\Skripsi;
use App\Models\User;
use App\Support\Processor;
use App\Support\Tokenizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\DB;
use Smalot\PdfParser\Parser as PdfParser;

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
                ->usingFileName($uploadedFile->getFilename())
                ->toMediaCollection();
        }

        $skripsi->saveKalimatsAndHashesFromDocument();

        DB::commit();

        SessionHelper::flashMessage(
            __("messages.create.success"),
            MessageState::STATE_SUCCESS,
        );

        return redirect()->back();
    }
}
