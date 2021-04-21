<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\GithupController;
use App\Http\Controllers\Api\Auth\TwitterController;
use App\Http\Controllers\Api\User\MeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
});
