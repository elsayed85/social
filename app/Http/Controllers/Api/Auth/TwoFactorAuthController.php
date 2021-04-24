<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\TwoFactorLoginRequest;
use Illuminate\Http\Request;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Laravel\Fortify\Contracts\FailedTwoFactorLoginResponse;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse;
use Laravel\Passport\TokenRepository;

class TwoFactorAuthController extends Controller
{
    public function enable(Request $request, EnableTwoFactorAuthentication $enable, TokenRepository $tokenRepository)
    {
        $enable($request->user());
        $token = $this->createToken($request, '2fa-pass');
        $request->user()->token()->revoke();
        return success(['status' => 'two-factor-authentication-enabled', 'token' => $token]);
    }

    public function disable(Request $request, DisableTwoFactorAuthentication $disable)
    {
        if (!$request->user()->two_factor_auth_enabled) {
            return failed('2fa is disabled', ['two_factor_auth_enabled' => false]);
        }

        $disable($request->user());
        return success(['status' => 'two-factor-authentication-disabled']);
    }

    public function showQrCode(Request $request)
    {
        if (!$request->user()->two_factor_auth_enabled) {
            return failed('2fa is disabled', ['two_factor_auth_enabled' => false]);
        }

        return success(['svg' => $request->user()->twoFactorQrCodeSvg()]);
    }

    public function showRecoveryCodes(Request $request)
    {
        if (!$request->user()->two_factor_auth_enabled) {
            return failed('2fa is disabled', ['two_factor_auth_enabled' => false]);
        }

        if (!$request->user()->two_factor_secret || !$request->user()->two_factor_recovery_codes) {
            return success([]);
        }

        return success(json_decode(decrypt(
            $request->user()->two_factor_recovery_codes
        ), true));
    }

    public function generateRecoveryCodes(Request $request, GenerateNewRecoveryCodes $generate)
    {
        if (!$request->user()->two_factor_auth_enabled) {
            return failed('2fa is disabled', ['two_factor_auth_enabled' => false]);
        }

        $generate($request->user());
        return success(['status' => 'recovery-codes-generated']);
    }

    public function login(TwoFactorLoginRequest $request)
    {
        if (!$request->user()->two_factor_auth_enabled) {
            return failed('2fa is disabled', ['two_factor_auth_enabled' => false]);
        }

        $user = $request->user();

        if ($code = $request->validRecoveryCode()) {
            $user->replaceRecoveryCode($code);
        } elseif (!$request->hasValidCode()) {
            return app(FailedTwoFactorLoginResponse::class);
        }

        $token = $this->createToken($request);
        return success(['status' => 'two-factor-authentication-loged-in', 'token' => $token]);
    }

    public function createToken(Request $request, ...$scopes)
    {
        $token = $request->user()->createToken('website', $scopes)->accessToken;
        cookie()->queue('refresh_token', $token, 14400,  null, null, false, true);
        return $token;
    }
}
