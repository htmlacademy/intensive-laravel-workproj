<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @param UserRequest $request
     * @return JsonResponse|Responsable
     */
    public function register(UserRequest $request)
    {
        $params = $request->safe()->except('file');
        $user = User::create($params);
        $token = $user->createToken('auth-token');

        return $this->success([
            'user' => $user,
            'token' => $token->plainTextToken,
        ], 201);
    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse|Responsable
     */
    public function login(LoginRequest $request)
    {
        if(!Auth::attempt($request->validated())) {
            // todo рассказать о возможности ограничения к-ва запросов на один роут от одного пользователя
            abort(401, trans('auth.failed'));
        }

        $token = Auth::user()->createToken('auth-token');

        return $this->success(['token' => $token->plainTextToken]);
    }

    /**
     * @return JsonResponse|Responsable
     */
    public function logout()
    {
        Auth::user()->tokens()->delete();

        return $this->success(null, 204);
    }
}
