<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Models\Show;

class EpisodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Show $show)
    {
        return response()->json($show->episodes);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Episode  $episode
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Episode $episode)
    {
        return response()->json($episode);
    }
}
