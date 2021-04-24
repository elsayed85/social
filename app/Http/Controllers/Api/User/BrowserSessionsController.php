<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Services\Api\LogoutOtherBrowserSessions;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class BrowserSessionsController extends Controller
{
    public function logoutOtherBrowserSessions(Request $request,  StatefulGuard $guard, LogoutOtherBrowserSessions $service)
    {
        if (!Hash::check($request->password, Auth::user()->password)) {
            throw ValidationException::withMessages([
                'password' => [__('This password does not match our records.')],
            ]);
        }

        $guard->logoutOtherDevices($request->password);

        $service->deleteOtherSessionRecords();

        return success(['sessions' => $service->sessions()]);
    }
}
