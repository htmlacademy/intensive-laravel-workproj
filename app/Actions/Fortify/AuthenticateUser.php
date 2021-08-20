<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Fortify;

class AuthenticateUser
{
    /**
     * Validate and create a newly registered user.
     *
     * @param array $input
     * @return array
     */
    public function login(array $input)
    {
        Validator::make($input, [
            Fortify::username() => 'required|string',
            'password' => 'required|string',
        ])->validate();

        if(!Auth::attempt(
            Arr::only($input, [Fortify::username(), 'password'])
        )) {
            // todo добавить вызов fortify limiter
            abort(401);
        }

        $token = Auth::user()->createToken('auth-token');

        return ['token' => $token->plainTextToken];
    }
}
