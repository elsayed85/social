<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\UpdateAvatarRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MeController extends Controller
{
    public function me()
    {
        return success(auth()->user());
    }

    public function statuses()
    {
        $user = auth()->user();
        return success([
            'verified_email' => $user->hasVerifiedEmail(),
            'verified_account' => $user->isVerified(),
            'baned' => $user->isBanned(),
        ]);
    }

    public function updataAvatar(UpdateAvatarRequest $request)
    {
        $avatar = auth()->user()
            ->addMedia($request->file('avatar'))
            ->usingFileName(Str::uuid() . "." . $request->file('avatar')->getClientOriginalExtension())
            ->toMediaCollection('avatar');
        return success([auth()->user()->avatar]);
    }

    public function verifyEmail()
    {
        $user = auth()->user();
        if (!$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }
        return success(['has_verified_email' => $user->hasVerifiedEmail()]);
    }
}
