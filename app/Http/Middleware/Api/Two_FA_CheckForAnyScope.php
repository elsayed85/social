<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Laravel\Passport\Exceptions\MissingScopeException;

class Two_FA_CheckForAnyScope
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$scopes
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$scopes)
    {
        if (!$request->user() || !$request->user()->token()) {
            throw new AuthenticationException;
        }

        if ($request->user()->two_factor_auth_enabled) {
            foreach ($scopes as $scope) {
                if ($request->user()->tokenCan($scope)) {
                    return $next($request);
                }
            }

            throw new MissingScopeException($scopes);
        }

        return $next($request);
    }
}
