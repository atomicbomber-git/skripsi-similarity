<?php

namespace App\Http\Controllers;

use App\Constants\MessageState;
use App\Helper\SessionHelper;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class SkripsiFileDownloadController extends Controller
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
     * @return RedirectResponse|Response|BinaryFileResponse
     */
    public function __invoke(Request $request, User $mahasiswa)
    {
        if ($mahasiswa->skripsi === null) {
            return $this->responseFactory->noContent(404);
        }

        $zip = new ZipArchive();
        $tmpZipPath = database_path('temporary.zip');
        if (file_exists($tmpZipPath)) {
            unlink($tmpZipPath);
        }

        if ($zip->open($tmpZipPath,  ZipArchive::CREATE)) {
            foreach ($mahasiswa->skripsi->media as $medium) {
                $zip->addFile(
                    $medium->getPath(),
                    $medium->file_name
                );
            }
            $zip->close();
        } else {
            SessionHelper::flashMessage(
                "Gagal memroses berkas skripsi",
                MessageState::STATE_DANGER,
            );

            unlink($tmpZipPath);
            return redirect()->back();
        }

        return $this->responseFactory->file(
            $tmpZipPath
        , [
            'Content-Disposition' => "attachment; filename={$mahasiswa->name} - ({$mahasiswa->skripsi->judul}).zip",
            'Content-Type' => 'application/zip',
        ]);
    }
}
