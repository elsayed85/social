<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class GithupController extends Controller
{
    public function login(Request $request)
    {
        $url =  Socialite::driver('github')->stateless()->redirect()->getTargetUrl();
        return success(['url' => $url]);
    }

    public function callback(Request $request)
    {
        try {
            $githupUser = Socialite::driver('github')->stateless()->user();
            $user = User::whereEmail($githupUser->email)->first();
            if ($user) {
                $user->update([
                    'token' => $githupUser->token,
                    'twitter_username' => $githupUser->user['twitter_username'],
                    'location' => $githupUser->user['location'],
                    'bio' => $githupUser->user['bio']
                ]);
                $token = $user->createToken("website")->accessToken;
                return success(['user_recently_created' => false, 'token' => $token]);
            }

            $user = User::create([
                'name' => $githupUser->name,
                'username' => SlugService::createSlug(User::class, 'username', $githupUser->nickname),
                'email' => $githupUser->email,
                'email_verified_at' => now(),
            ]);

            $user->githup()->create([
                'token' => $githupUser->token,
                'username' => $githupUser->nickname,
                'githup_id' => $githupUser->user['id'],
                'twitter_username' => $githupUser->user['twitter_username'],
                'location' => $githupUser->user['location'],
                'bio' => $githupUser->user['bio']
            ]);

            $token = $user->createToken("website")->accessToken;
            return success(['user_recently_created' => true, 'token' => $token]);
        } catch (\Throwable $th) {
            return failed($th->getMessage());
        }
    }
}
