<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Requests\VoteRequest;
use App\Models\Episode;
use App\Models\Show;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Обновление профиля.
     *
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserRequest $request)
    {
        $params = $request->safe()->except('file');

        $user = Auth::user();
        $path = false;

        if($request->hasFile('file')) {
            $oldFile = $user->avatar;
            $result = $request->file('file')->store('avatars', 'public');
            $path = $result ? $request->file('file')->hashName() : false;
            $params['avatar'] = $path;
        }

        $user->update($params);

        if($path) {
            Storage::disk('public')->delete($oldFile);
        }

        return response()->json(Auth::user()->makeVisible('email'));
    }

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

    public function watchShow(Show $show)
    {
        Auth::user()->shows()->attach($show);

        return response()->json(null, 201);
    }

    public function unwatchShow(Show $show)
    {
        Auth::user()->shows()->detach($show);

        return response()->json(null, 201);
    }

    public function watchEpisode(Episode $episode)
    {
        Auth::user()->episodes()->attach($episode);

        return response()->json(null, 201);
    }

    public function unwatchEpisode(Episode $episode)
    {
        Auth::user()->episodes()->detach($episode);

        return response()->json(null, 201);
    }

    public function vote(VoteRequest $request, Show $show)
    {
        Auth::user()->shows()->attach($show, ['vote' => $request->vote]);

        return response()->json(null, 201);
    }
}
