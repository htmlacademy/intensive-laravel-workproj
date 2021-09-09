<?php

namespace App\Http\Controllers;

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
}
