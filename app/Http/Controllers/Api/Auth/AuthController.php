<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\ForgetPasswordRequest;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\User;
use App\Traits\AuthTrait;
use App\Utilities\ProxyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    use AuthTrait;

    protected $proxy;

    public function __construct(ProxyRequest $proxy)
    {
        $this->proxy = $proxy;
        $this->middleware(['guest'])->except(['logout']);
        $this->middleware(['auth:api'])->only(['logout']);
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->only(['name', 'email']);
        $data['password'] =  Hash::make($request->password);
        $user = User::create($data);
        $resp = $this->proxy->grantPasswordToken($user->email, request('password'));
        return success([
            'token' => $resp->access_token,
            'expiresIn' => $resp->expires_in,
        ]);
    }

    public function login(LoginRequest $request)
    {
        $data = ['password' =>  $request->password, $this->findUsername() => $request->login_key];
        if (Auth::attempt($data)) {
            $user = auth()->user();
            $resp = $this->proxy->grantPasswordToken($user->email, request('password'));
            return success([
                'token' => $resp->access_token,
                'expiresIn' => $resp->expires_in,
            ]);
        }
        return failed("failed to login");
    }

    public function forgetPassword(ForgetPasswordRequest $request)
    {
        $status = Password::sendResetLink($request->only('email'));
        if ($status === Password::RESET_LINK_SENT) {
            return success(['message' => "check your email"]);
        }
        return failed(__($status));
    }

    public function refreshToken()
    {
        try {
            $resp = $this->proxy->refreshAccessToken();
        } catch (\Throwable $th) {
            return failed($th->getMessage(), ['expired' => true]);
        }

        return success([
            'token' => $resp->access_token,
            'expiresIn' => $resp->expires_in,
            'message' => 'Token has been refreshed.',
        ]);
    }

    public function logout()
    {
        request()->user()->token()->delete();

        // remove the httponly cookie
        cookie()->queue(cookie()->forget('refresh_token'));

        return success([
            'message' => 'You have been successfully logged out',
        ]);
    }
}
