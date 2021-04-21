<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\GithupController;
use App\Http\Controllers\Api\Auth\TwitterController;
use App\Http\Controllers\Api\User\MeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->prefix('auth')->as('auth.')->group(function () {
    Route::post('register', [AuthController::class, "register"])->name('register');
    Route::post('login', [AuthController::class, "login"])->name('login');
    Route::post('forget-password', [AuthController::class, "forgetPassword"])->name('forget_password');

    Route::get('github/login', [GithupController::class, "login"])->name('login');
    Route::get('github/callback', [GithupController::class, "callback"])->name('callback');
    
    Route::get('twitter/login', [TwitterController::class, "login"])->name('login');
    Route::get('twitter/callback', [TwitterController::class, "callback"])->name('callback');
});

Route::middleware(['auth:sanctum'])->prefix('user')->as('user.')->group(function () {
    Route::get('me', [MeController::class, "me"])->name('me');
    Route::post('verify-email', [MeController::class, "verifyEmail"])->name('verify_email');
});
