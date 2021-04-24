<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordConfirmationApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user) {
            throw new AuthenticationException;
        }

        if (!$request->password) {
            throw ValidationException::withMessages(['password' => 'You need to enter the password to Access Be Authorized For This Action']);
        }

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['password' => 'password is incorrect']);
        }

        return $next($request);
    }
}
