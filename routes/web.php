<?php

use App\Events\TestEvents;
use App\Http\Controllers\Api\Auth\TwitterController;
use App\Models\Posts\Post;
use App\Models\User;
use App\Services\Timezone;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::view('test', 'test');

Route::get('follow', function () {
    $user1 = User::find(3);
    // $user2 = User::find(2);

    $user1->follow(User::find(1));

    // User::all()->random(10)->each(function($user2) use($user1){
    //     $user1->follow($user2);
    // });


    // dd([
    //     'user1' => [
    //         'followings' => $user1->followings,
    //         'followers' => $user1->followers,
    //         'is_followed_by_user2' => $user1->isFollowedBy(2),
    //         'is_follow_user2' => $user1->isFollowing(2),
    //     ],
    //     'user2' => [
    //         'followings' => $user2->followings,
    //         'followers' => $user2->followers,
    //         'is_followed_by_user1' => $user2->isFollowedBy(1),
    //         'is_follow_user1' => $user2->isFollowing(1),
    //     ]
    // ]);
});


Route::get('follow', function () {
    $user1 = User::find(3);
    // $user2 = User::find(2);

    $user1->follow(User::find(1));

    // User::all()->random(10)->each(function($user2) use($user1){
    //     $user1->follow($user2);
    // });


    // dd([
    //     'user1' => [
    //         'followings' => $user1->followings,
    //         'followers' => $user1->followers,
    //         'is_followed_by_user2' => $user1->isFollowedBy(2),
    //         'is_follow_user2' => $user1->isFollowing(2),
    //     ],
    //     'user2' => [
    //         'followings' => $user2->followings,
    //         'followers' => $user2->followers,
    //         'is_followed_by_user1' => $user2->isFollowedBy(1),
    //         'is_follow_user1' => $user2->isFollowing(1),
    //     ]
    // ]);
});


Route::get('test2', function () {
    auth()->loginUsingId(1);

    event(new TestEvents(auth()->id(), ['new_post' => ['content' => "joithjreu9iothurewhytu9hrtu9yhrt9uh"]]));

    // $user = auth()->user();

    // $recoveryCodes = $user->recoveryCodes();

    // $twoFactorQrCodeSvg = $user->twoFactorQrCodeSvg();

    // $twoFactorQrCodeUrl = $user->twoFactorQrCodeUrl();


    // dd($recoveryCodes , $twoFactorQrCodeSvg , $twoFactorQrCodeUrl);



    // $user1 = User::find(1);
    // $posts = Post::take(10)->Trashed()->with(['LoveReactions'])->get();
    // // $user1->ban(['comment' => "randome comment " . rand(1, 300), 'expired_at' => now()->addMinutes(rand(1, 5))]);
    // // //$user1->ban(['comment' => "randome comment " . rand(1, 300), 'expired_at' => null()]);

    // $post = $posts->where("id", 1)->first();
    // // $post->restore();
    // // $post->publishNow()
    // //$post->toggleLove();
    // //dd(Post::OrderByLikesCount()->get()->random(10));

    // //$post->love();
    // dd($post->collectLovers());

    // dd($post->isDraft(), $post->isTrashed(), $post->isPublished());
});


Route::get('test3', function () {
    dd(User::first()->isVerified());
});
