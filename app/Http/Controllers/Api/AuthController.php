<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\ForgetPasswordRequest;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\User;
use App\Traits\AuthTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    use AuthTrait;

    public function __construct()
    {
        $this->middleware(['guest']);
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->only(['name', 'email']);
        $data['password'] =  Hash::make($request->password);
        $user = User::create($data);
        $token = $user->createToken("website")->plainTextToken;
        return success(['token' => $token]);
    }

    public function login(LoginRequest $request)
    {
        $data = ['password' =>  $request->password, $this->findUsername() => $request->login_key];
        if (Auth::attempt($data)) {
            $user = auth()->user();
            $token = $user->createToken("website")->plainTextToken;
            return success(['token' => $token]);
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
}
