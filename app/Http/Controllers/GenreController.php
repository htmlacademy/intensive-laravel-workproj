<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenreUpdateRequest;
use App\Models\Genre;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;

class GenreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse|Responsable
     */
    public function index()
    {
        return $this->success(Genre::all());
    }

    public function update(GenreUpdateRequest $request, Genre $genre)
    {
        $genre->update($request->validated());

        return $this->success($genre->fresh());
    }
}
