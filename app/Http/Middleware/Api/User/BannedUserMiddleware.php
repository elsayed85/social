<?php

namespace App\Http\Middleware\Api\User;

use Closure;
use Illuminate\Http\Request;

class BannedUserMiddleware
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
        if ($user && $user->isBanned()) {
            $bans = $user->bans->map(function ($ban) {
                return [
                    "comment" => $ban->comment,
                    'expired_at' => $ban->expired_at,
                    'expired_at_for_human' => optional($ban->expired_at)->diffForHumans(),
                    'is_permanent' => $ban->isPermanent(),
                    'is_temporary' => $ban->isTemporary()
                ];
            });
            return failed("You Are Banned", [
                "bans" => $bans
            ]);
        }
        return $next($request);
    }
}
