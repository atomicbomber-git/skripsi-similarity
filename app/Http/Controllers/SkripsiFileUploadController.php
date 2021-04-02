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
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\DB;
use Smalot\PdfParser\Parser as PdfParser;

class SkripsiFileUploadController extends Controller
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
            "judul" => ["required", "string", "max:10000"],
            "skripsi" => ["file", "mimetypes:application/vnd.openxmlformats-officedocument.wordprocessingml.document"],
        ]);

        DB::beginTransaction();

        /** @var Skripsi $skripsi */
        $skripsi = $mahasiswa->skripsi()->create([
            "judul" => $data["judul"]
        ]);

        $documentMedia = $skripsi
            ->addMediaFromRequest("skripsi")
            ->toMediaCollection();

        $tokenizer = new Tokenizer();
        $tokenizer->load($skripsi->getDomDocument());

        $sentenceAndHashes = collect($tokenizer->tokenize())
            ->pluck("value")
            ->filter(fn($sentence) => mb_strlen($sentence) > 0)
            ->map(fn($sentence) => [
                "text" => $sentence,
                "hashes" => $this->processor->textToFingerprintHashes($sentence)
            ])
            ->filter(fn($sentenceAndHash) => $sentenceAndHash["hashes"] !== []);

        DB::beginTransaction();

        $sentenceAndHashes->each(function (array $sentenceAndHash) use ($skripsi) {
            $kalimatSkripsi = $skripsi->kalimatSkripsis()->create([
                "teks" => $sentenceAndHash["text"],
            ]);

            KalimatHash::query()->insert(
                array_map(
                    fn($hash, $position) => [
                        "kalimat_skripsi_id" => $kalimatSkripsi->getKey(),
                        "position" => $position,
                        "hash" => $hash,
                        "created_at" => now(),
                        "updated_at" => now(),
                    ],
                    $sentenceAndHash["hashes"],
                    array_keys($sentenceAndHash["hashes"])
                )
            );
        });

        DB::commit();

        SessionHelper::flashMessage(
            __("messages.create.success"),
            MessageState::STATE_SUCCESS,
        );

        return redirect()->back();
    }
}
