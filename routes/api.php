<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('register', [AuthController::class, "register"])->name('register');
Route::post('login', [AuthController::class, "login"])->name('login');
Route::post('forget-password', [AuthController::class, "forgetPassword"])->name('forget_password');


Route::middleware(['auth:sanctum'])->prefix('user')->as('user.')->group(function () {
    Route::get('me', [MeController::class, "me"])->name('me');
});
