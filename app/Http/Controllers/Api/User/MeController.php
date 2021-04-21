<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function me()
    {
        return success(auth()->user());
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
