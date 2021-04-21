<?php

use App\Http\Controllers\Api\Auth\TwitterController;
use App\Models\User;
use App\Services\Timezone;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');


Route::get('folow', function () {
    $user1 = User::find(1);
    $user2 = User::find(2);

    $user1->follow($user2);


    dd([
        'user1' => [
            'followings' => $user1->followings,
            'followers' => $user1->followers,
            'is_followed_by_user2' => $user1->isFollowedBy(2),
            'is_follow_user2' => $user1->isFollowing(2),
        ],
        'user2' => [
            'followings' => $user2->followings,
            'followers' => $user2->followers,
            'is_followed_by_user1' => $user2->isFollowedBy(1),
            'is_follow_user1' => $user2->isFollowing(1),
        ]
    ]);
});


Route::get('block', function () {
    $user1 = User::find(1);
    $user2 = User::find(2);

    $user1->block($user2->id);
    $user2->block($user1->id);


    dd([
        'user1' => [
            'blockers' => $user1->blockerUsers()->get(),
            'blocked' => $user1->blockingUsers()->get(),
            'is_block_user2' => $user1->isBlocking($user2->id),
            'is_blockedBy_user2' => $user1->isBlockedBy($user2->id),
            'is_mutual_block' => $user1->isMutualBlock($user2->id),
        ],
        'user2' => [
            'blockers' => $user2->blockerUsers()->get(),
            'blocked' => $user2->blockingUsers()->get(),
            'is_block_user1' => $user2->isBlocking($user1->id),
            'is_blockedBy_user1' => $user2->isBlockedBy($user1->id),
            'is_mutual_block' => $user2->isMutualBlock($user1->id),
        ]
    ]);
});


Route::get('twitter/login', [TwitterController::class, "login"])->name('twitter.login');
Route::get('twitter/callback', [TwitterController::class, "callback"])->name('twitter.callback');
