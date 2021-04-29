<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\ChangePasswordRequest;
use App\Http\Requests\Api\User\UpdateAvatarRequest;
use App\Http\Requests\Api\User\UpdateCoverRequest;
use App\Http\Requests\Api\User\UpdateInfoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
            '2fa_enabled' => $user->two_factor_auth_enabled
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

    public function updataCover(UpdateCoverRequest $request)
    {
        $avatar = auth()->user()
            ->addMedia($request->file('cover'))
            ->usingFileName(Str::uuid() . "." . $request->file('cover')->getClientOriginalExtension())
            ->toMediaCollection('cover');
        return success(["cover" => auth()->user()->cover]);
    }

    public function verifyEmail()
    {
        $user = auth()->user();
        if (!$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }
        return success(['has_verified_email' => $user->hasVerifiedEmail()]);
    }

    public function updateInfo(UpdateInfoRequest $request)
    {
        if ($request->hasAny($inputs = ['name', 'email', 'username'])) {
            $extra = ($request->has('email') && $request->email != auth()->user()->email) ? ['email_verified_at' => null] : [];
            auth()->user()->update(array_merge($request->only($inputs), $extra));
            count($extra) ? auth()->user()->sendEmailVerificationNotification() : null;
            // this is stupid, I know but I need to sleep right now :'(
            return success(['user' => auth()->user()]);
        }
        return failed('nothing changed');
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        auth()->user()->update(['password' => Hash::make($request->new_password)]);
        return response()->noContent();
    }
}
