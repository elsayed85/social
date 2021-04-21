<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auth\Twitter;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class TwitterController extends Controller
{
    public function login(Request $request)
    {
        $url =  Socialite::driver('twitter')->redirect()->getTargetUrl();
        return success(['url' => $url]);
    }

    public function callback(Request $request)
    {
        try {
            $twitterUser = Socialite::driver('twitter')->user();
            return success(['twitter' => [
                'twitter_id' => $twitterUser->id,
                'username' => $twitterUser->nickname,
                'name' => $twitterUser->name,
                'email' => $twitterUser->email,
                'location' => $twitterUser->user['location'],
                'bio' => $twitterUser->user['description'],
                'avatar_original' => $twitterUser->avatar_original,
                'avatar' => $twitterUser->avatar,
                'banner_url' => $twitterUser->user['profile_banner_url']
            ]]);
        } catch (\Throwable $th) {
            return failed($th->getMessage());
        }
    }
}
