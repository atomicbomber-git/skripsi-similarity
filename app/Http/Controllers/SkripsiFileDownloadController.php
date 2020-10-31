<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
     * @return Response|BinaryFileResponse
     */
    public function __invoke(Request $request, User $mahasiswa)
    {
        if ($mahasiswa->skripsi === null) {
            return $this->responseFactory->noContent(404);
        }

        return $this->responseFactory->file(
            $mahasiswa->skripsi->getFirstMediaPath(),
        );
    }
}
