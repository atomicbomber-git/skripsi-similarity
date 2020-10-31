<?php

namespace App\Http\Controllers;

use App\Constants\MessageState;
use App\Helper\SessionHelper;
use App\Models\Skripsi;
use App\Models\SkripsiFingerprintHash;
use App\Models\User;
use App\Support\Processor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\DB;
use Smalot\PdfParser\Parser as PdfParser;

class UploadFileUploadController extends Controller
{
    private ResponseFactory $responseFactory;
    private PdfParser $pdfParser;
    private Processor $processor;

    public function __construct(ResponseFactory $responseFactory, PdfParser $pdfParser, Processor $processor)
    {
        $this->responseFactory = $responseFactory;
        $this->pdfParser = $pdfParser;
        $this->processor = $processor;
    }

    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     */
    public function __invoke(Request $request, User $mahasiswa)
    {
        $data = $request->validate([
            "judul" => ["required", "string", "max:100"],
            "skripsi" => ["file", "mimes:pdf"],
        ]);

        DB::beginTransaction();

        /** @var Skripsi $skripsi */
        $skripsi = $mahasiswa->skripsi()->create([
            "judul" => $data["judul"]
        ]);

        $media = $skripsi
            ->addMediaFromRequest("skripsi")
            ->toMediaCollection();

        $text = $this->pdfParser->parseFile(
            $media->getPath()
        )->getText();

        $hashes = $this->processor->textToFingerprintHashes($text);

        DB::table((new SkripsiFingerprintHash)->getTable())
            ->insert(
                array_map(
                    function ($position, $hash) use ($skripsi) {
                        return [
                            "skripsi_id" => $skripsi->id,
                            "position" => $position,
                            "hash" => $hash,
                        ];
                    },
                    array_keys($hashes), $hashes,
                )
            );

        DB::commit();

        SessionHelper::flashMessage(
            __("messages.create.success"),
            MessageState::STATE_SUCCESS,
        );

        return redirect()->back();
    }
}
