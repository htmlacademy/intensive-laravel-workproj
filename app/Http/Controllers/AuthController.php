<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create($request->validated());
        $token = $user->createToken('auth-token');

        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken,
        ], 201);
    }

    /**
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        if(!Auth::attempt($request->validated())) {
            // todo рассказать о возможности ограничения к-ва запросов на один роут от одного пользователя
            abort(401);
        }

        $token = Auth::user()->createToken('auth-token');

        return response()->json(['token' => $token->plainTextToken]);
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();

        return response()->json([], 204);
    }
}
