<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\GithupController;
use App\Http\Controllers\Api\Auth\TwitterController;
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
    Route::post('verify-email', [MeController::class, "verifyEmail"])->name('verify_email');
    Route::post('logout', [AuthController::class, "logout"])->name('logout');

    Route::prefix('follow-sys')->group(function () {
        Route::post('follow', [FollowController::class, "follow"])->name('follow');
        Route::post('unfollow', [FollowController::class, "unfollow"])->name('unfollow');
        Route::get('followings', [FollowController::class, "followingsList"])->name('followings_list');
        Route::get('followers', [FollowController::class, "followersList"])->name('followers_list');
        Route::post('is-followed-by-user', [FollowController::class, "isFollowedByUser"])->name('is_followed_by_user');
        Route::post('is-follow-user', [FollowController::class, "isFollowUser"])->name('is_follow_user');
    });
});
