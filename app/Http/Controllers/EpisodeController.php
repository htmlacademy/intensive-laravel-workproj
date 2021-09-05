<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Models\Show;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;

class EpisodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse|Responsable
     */
    public function index(Show $show)
    {
        return $this->success($show->episodes);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Episode  $episode
     * @return JsonResponse|Responsable
     */
    public function show(Episode $episode)
    {
        return $this->success($episode);
    }
}
