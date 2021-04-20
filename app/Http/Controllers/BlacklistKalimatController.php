<?php

namespace App\Http\Controllers;

use App\Constants\MessageState;
use App\Events\SkripsiModified;
use App\Helper\SessionHelper;
use App\Models\BlacklistKalimat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Validation\Rule;

class BlacklistKalimatController extends Controller
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
        return $this->responseFactory->view("blacklist-kalimat.index", [
            "kalimats" => BlacklistKalimat::query()
                ->when($request->get("search"), function (Builder $builder, string $term) {
                    $builder->orderByRaw("teks <-> ?", [$term]);
                })
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
        return $this->responseFactory->view("blacklist-kalimat.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            "teks" => ["required", "string", Rule::unique(BlacklistKalimat::class), "max:20000"],
        ]);

        BlacklistKalimat::query()->create([
            "teks" => trim($data["teks"])
        ]);

        SessionHelper::flashMessage(
            __("messages.create.success"),
            MessageState::STATE_SUCCESS,
        );

        event(new SkripsiModified);

        return $this->responseFactory->redirectToRoute("blacklist-kalimat.index");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param BlacklistKalimat $blacklistKalimat
     * @return Response
     */
    public function edit(BlacklistKalimat $blacklistKalimat)
    {
        return $this->responseFactory->view("blacklist-kalimat.edit", [
            "kalimat" => $blacklistKalimat,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param BlacklistKalimat $blacklistKalimat
     * @return RedirectResponse
     */
    public function update(Request $request, BlacklistKalimat $blacklistKalimat)
    {
        $data = $request->validate([
            "teks" => ["required", "string", Rule::unique(BlacklistKalimat::class)->ignoreModel($blacklistKalimat), "max:20000"],
        ]);

        $blacklistKalimat->update([
            "teks" => trim($data["teks"])
        ]);

        SessionHelper::flashMessage(
            __("messages.update.success"),
            MessageState::STATE_SUCCESS,
        );

        event(new SkripsiModified);

        return $this->responseFactory->redirectToRoute("blacklist-kalimat.edit", $blacklistKalimat);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param BlacklistKalimat $blacklistKalimat
     * @return RedirectResponse
     */
    public function destroy(BlacklistKalimat $blacklistKalimat)
    {
        $blacklistKalimat->forceDelete();

        SessionHelper::flashMessage(
            __("messages.delete.success"),
            MessageState::STATE_SUCCESS,
        );

        event(new SkripsiModified);
        return $this->responseFactory->redirectToRoute("blacklist-kalimat.index");
    }
}
