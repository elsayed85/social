<?php

namespace App\Http\Controllers\Api\Auth;

use Abraham\TwitterOAuth\TwitterOAuth;
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
            $tokens = $this->access_token($request->oauth_token, $request->oauth_verifier);
            $twitterUser = Socialite::driver('twitter')->userFromTokenAndSecret($tokens->oauth_token, $tokens->oauth_token_secret);
            return success(['twitter' => [
                'twitter_id' => $twitterUser->id,
                'username' => $twitterUser->nickname,
                'name' => $twitterUser->name,
                'email' => $twitterUser->email,
                'avatar_original' => str_replace('_normal', '', $twitterUser->avatar),
                'avatar' => $twitterUser->avatar,
            ]]);
        } catch (\Throwable $th) {
            return failed($th->getMessage());
        }
    }

    public function twitter_redirect(Request $request)
    {
        $tokens = $this->access_token($request->oauth_token, $request->oauth_verifier);
        $user = Socialite::driver('twitter')->userFromTokenAndSecret($tokens->oauth_token, $tokens->oauth_token_secret);
        return response()->json($user, 201);
    }


    private function access_token($oauth_token, $oauth_verifier)
    {
        $config = config('services')['twitter'];
        $connection = new TwitterOAuth($config['client_id'], $config['client_secret']);
        $tokens = $connection->oauth("oauth/access_token", ["oauth_verifier" => $oauth_verifier, "oauth_token" => $oauth_token]);
        return (object) $tokens;
    }
}
