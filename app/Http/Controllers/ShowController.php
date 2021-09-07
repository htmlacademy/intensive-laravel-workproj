<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddShowRequest;
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
        return $this->paginate(Show::paginate());
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
        return $this->success(null, 201);
    }
}
