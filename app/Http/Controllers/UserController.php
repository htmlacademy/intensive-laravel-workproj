<?php

namespace App\Http\Controllers;

use App\Exceptions\RequestException;
use App\Http\Requests\UserRequest;
use App\Http\Requests\VoteRequest;
use App\Models\Episode;
use App\Models\Show;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Обновление профиля.
     *
     * @param UserRequest $request
     * @return JsonResponse|Responsable
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

        return $this->success(Auth::user()->makeVisible('email'));
    }

    /**
     * Получение списка сериалов просматриваемых пользователем.
     *
     * @return JsonResponse|Responsable
     */
    public function shows()
    {
        return $this->success(Auth::user()->shows);
    }

    /**
     * Получение списка не просмотренных пользователем серий указанного сериала.
     *
     * @param Show $show
     * @return JsonResponse|Responsable
     */
    public function newEpisodes(Show $show)
    {
        $watched = Auth::user()->episodes()->where('show_id', $show->id)->pluck('id');

        return $this->success($show->episodes()->whereNotIn('id', $watched)->get());
    }

    /**
     * Добавление сериала в список просматриваемых.
     *
     * @param Show $show
     * @return JsonResponse|Responsable
     */
    public function watchShow(Show $show)
    {
        Auth::user()->shows()->attach($show);

        return $this->success(null, 201);
    }

    /**
     * Удаление сериала из списка просматриваемых.
     *
     * @param Show $show
     * @return JsonResponse|Responsable
     */
    public function unwatchShow(Show $show)
    {
        $this->validateWatchingShow($show);

        Auth::user()->shows()->detach($show);

        return $this->success(null, 201);
    }

    /**
     * Добавление эпизода в список просмотренных.
     *
     * @param Episode $episode
     * @return JsonResponse|Responsable
     */
    public function watchEpisode(Episode $episode)
    {
        $this->validateWatchingEpisode($episode);

        Auth::user()->episodes()->attach($episode);

        return $this->success(null, 201);
    }

    /**
     * Удаление эпизода из списка просмотренных.
     *
     * @param Episode $episode
     * @return JsonResponse|Responsable
     */
    public function unwatchEpisode(Episode $episode)
    {
        $this->validateWatchingEpisode($episode, true);

        Auth::user()->episodes()->detach($episode);

        return $this->success(null, 201);
    }

    /**
     * Выставление оценки сериалу.
     *
     * @param VoteRequest $request
     * @param Show $show
     * @return JsonResponse|Responsable
     */
    public function vote(VoteRequest $request, Show $show)
    {
        Auth::user()->shows()->syncWithPivotValues($show, ['vote' => $request->vote], false);

        return $this->success(null, 201);
    }

    private function validateWatchingShow(Show $show)
    {
        $user = Auth::user();

        if(!$user->shows()->where('show_id', $show->id)->exists()) {
            throw new RequestException('Сериал не входит список просматриваемых пользователем.');
        }
    }

    private function validateWatchingEpisode(Episode $episode, bool $unwatch = false)
    {
        $user = Auth::user();

        $this->validateWatchingShow($episode->show);

        if($unwatch && !$user->episodes()->where('episode_id', $episode->id)->exists()) {
            throw new RequestException('Эпизод не входит список просматриваемых пользователем.');
        }
    }
}
