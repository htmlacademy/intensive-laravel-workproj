<?php

namespace App\Http\Controllers;

use App\Models\Show;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Получение списка сериалов просматриваемых пользователем.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function shows()
    {
        return response()->json(Auth::user()->shows);
    }

    /**
     * Получение списка не просмотренных пользователем серий указанного сериала.
     *
     * @param Show $show
     * @return \Illuminate\Http\JsonResponse
     */
    public function newEpisodes(Show $show)
    {
        $watched = Auth::user()->episodes()->where('show_id', $show->id)->pluck('id');

        return response()->json($show->episodes()->whereNotIn('id', $watched)->get());
    }
}
