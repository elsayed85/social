<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\GithupController;
use App\Http\Controllers\Api\Auth\TwitterController;
use App\Http\Controllers\Api\Auth\TwoFactorAuthController;
use App\Http\Controllers\Api\User\BlockController;
use App\Http\Controllers\Api\User\FollowController;
use App\Http\Controllers\Api\User\MeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

DB::enableQueryLog();

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['guest'])->group(function () {
    Route::post('register', [AuthController::class, "register"])->name('register');
    Route::post('login', [AuthController::class, "login"])->name('login');
    Route::post('forget-password', [AuthController::class, "forgetPassword"])->name('forget_password');
    Route::post('refresh-token', [AuthController::class, "refreshToken"])->name('refresh_token');

    Route::get('github/login', [GithupController::class, "login"])->name('githup.login');
    Route::get('github/callback', [GithupController::class, "callback"])->name('githup.callback');

    Route::get('twitter/login', [TwitterController::class, "login"])->name('twitter.login');
    Route::get('twitter/callback', [TwitterController::class, "callback"])->name('twitter.callback');
});

Route::middleware(['auth:api'])->prefix('user')->as('user.')->group(function () {
    Route::get('me', [MeController::class, "me"])->name('me');
    Route::post('logout', [AuthController::class, "logout"])->name('logout');
    Route::get('statuses', [MeController::class, "statuses"])->name('statuses');
    Route::post('2fa/login', [TwoFactorAuthController::class, "login"])->name('two_fa.login');

    Route::middleware(['banned-user-api'])->group(function () {
        Route::post('update-avatar', [MeController::class, "updataAvatar"])->name('updata_avatar');
        Route::post('update-cover', [MeController::class, "updataCover"])->name('updata_cover');
        Route::post('update-info', [MeController::class, "updateInfo"])->name('update_info');
        Route::post('chnage-password', [MeController::class, "changePassword"])->name('change_password');

        Route::middleware(['2fa_scope:2fa-pass'])->group(function () {
            Route::post('logout-other-browser-sessions', [MeController::class, "logoutOtherBrowserSessions"])
                ->name('logout_other_browser_sessions')
                ->middleware(['password_api.confirm']);

            Route::prefix('2fa')->as('two_fa.')->middleware(['password_api.confirm'])->group(function () {
                Route::post('enable', [TwoFactorAuthController::class, "enable"])->name('enable');
                Route::post('disable', [TwoFactorAuthController::class, "disable"])->name('disable');
                Route::post('generate-recovery-codes', [TwoFactorAuthController::class, "generateRecoveryCodes"])->name('generate_recovery_codes');
                Route::post('show-qr-code', [TwoFactorAuthController::class, "showQrCode"])->name('show_qr_code');
                Route::post('show-recovery-codes', [TwoFactorAuthController::class, "showRecoveryCodes"])->name('show_recovery_codes');
            });

            Route::post('verify-email', [MeController::class, "verifyEmail"])->name('verify_email');

            Route::prefix('follow-sys')->group(function () {
                Route::post('follow', [FollowController::class, "follow"])->name('follow');
                Route::post('unfollow', [FollowController::class, "unfollow"])->name('unfollow');
                Route::post('toggle-follow', [FollowController::class, "toggleFollow"])->name('toggle_follow');
                Route::post('has-requested-to-follow', [FollowController::class, "hasRequestedToFollow"])->name('has_requested_to_follow');
                Route::post('accept-follow-request-from', [FollowController::class, "acceptFollowRequestFrom"])->name('accept_follow_request_from');
                Route::post('reject-follow-request-from', [FollowController::class, "rejectFollowRequestFrom"])->name('reject_follow_request_from');
                Route::post('are-following-each-other', [FollowController::class, "areFollowingEachOther"])->name('are_following_each_other');
                Route::post('is-followed-by-user', [FollowController::class, "isFollowedByUser"])->name('is_followed_by_user');
                Route::post('is-follow-user', [FollowController::class, "isFollowUser"])->name('is_follow_user');
                Route::get('followings', [FollowController::class, "followingsList"])->name('followings_list');
                Route::get('followers', [FollowController::class, "followersList"])->name('followers_list');
            });

            Route::prefix('block-sys')->group(function () {
                Route::post('block', [BlockController::class, "block"])->name('block');
                Route::post('unblock', [BlockController::class, "unblock"])->name('unblock');
                Route::post('is-blocked-by-user', [BlockController::class, "isBlockedByUser"])->name('is_blocked_by_user');
                Route::post('is-blocking-user', [BlockController::class, "isBlockingUser"])->name('is_blocking_user');
                Route::post('is-mutual-block', [BlockController::class, "isMutualBlock"])->name('is_mutual_block');
                Route::get('blocked-users', [BlockController::class, "blockedUsersList"])->name('blocked_users');
            });
        });
    });
});
