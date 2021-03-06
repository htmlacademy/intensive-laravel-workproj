<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddShowRequest;
use App\Jobs\AddShow;
use App\Models\Show;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;

class ShowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse|Responsable
     */
    public function index()
    {
        return $this->paginate(Show::select(['id', 'title'])->paginate(8));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Show  $show
     * @return JsonResponse|Responsable
     */
    public function show(Show $show)
    {
        return $this->success($show);
    }

    public function request(AddShowRequest $request)
    {
        AddShow::dispatch($request->imdb);

        return $this->success(null, 201);
    }
}
