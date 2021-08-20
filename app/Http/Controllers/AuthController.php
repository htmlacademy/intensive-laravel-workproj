<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return array
     */
    public function login(LoginRequest $request)
    {
        if(!Auth::attempt($request->validated())) {
            // todo рассказать о возможности ограничения к-ва запросов на один роут от одного пользователя
            abort(401);
        }

        $token = Auth::user()->createToken('auth-token');

        return ['token' => $token->plainTextToken];
    }
}
