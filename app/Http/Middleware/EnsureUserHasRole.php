<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!method_exists(User::class, $role) ) {
            throw new \LogicException("Метод $role отсутствует у модели пользователя");
        }

        if (!Auth::check() || !Auth::user()->$role()) {
            throw new AuthorizationException();
        }

        return $next($request);
    }
}
