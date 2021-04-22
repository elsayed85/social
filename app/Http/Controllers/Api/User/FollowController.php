<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\Follow\AcceptFollowRequestFromRequest;
use App\Http\Requests\Api\User\Follow\AreFollowingEachOtherRequest;
use App\Http\Requests\Api\User\Follow\FollowersListRequest;
use App\Http\Requests\Api\User\Follow\FollowingsListRequest;
use App\Http\Requests\Api\User\Follow\FollowRequest;
use App\Http\Requests\Api\User\Follow\HasRequestedToFollowRequest;
use App\Http\Requests\Api\User\Follow\IsFollowedByUserRequest;
use App\Http\Requests\Api\User\Follow\IsFollowUserRequest;
use App\Http\Requests\Api\User\Follow\RejectFollowRequestFromRequest;
use App\Http\Requests\Api\User\Follow\ToggleFollowRequest;
use App\Http\Requests\Api\User\Follow\UnFollowRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FollowController extends Controller
{
    public function follow(FollowRequest $request)
    {
        $authUser = auth()->user()->load("followings");
        $user2 = $request->user2;
        $currentUserFollowUser = $authUser->followings->contains($user2);

        if ($currentUserFollowUser) {
            $followRequestIsAccepted = $authUser->isFollowingAndAccepted($user2);
            if (!$followRequestIsAccepted) {
                return failed("you already follow, but follow request is not accepted yet", ['currentUserFollowUser' => $currentUserFollowUser, 'follow_request_is_accepted' => $followRequestIsAccepted]);
            }
            return failed("you already follow {$user2->name}", ['currentUserFollowUser' => $currentUserFollowUser, 'follow_request_is_accepted' => $followRequestIsAccepted]);
        }

        $authUser->follow($user2);
        return response()->noContent();
    }

    public function unfollow(UnFollowRequest $request)
    {
        $authUser = auth()->user()->load("followings");
        $user2 = $request->user2;
        $currentUserFollowUser = $authUser->followings->contains($user2);
        if (!$currentUserFollowUser) {
            return failed("you already are not following {$user2->name}", ['currentUserFollowUser' => $currentUserFollowUser]);
        }
        $authUser->unfollow($user2);
        return response()->noContent();
    }

    public function toggleFollow(ToggleFollowRequest $request)
    {
        $authUser = auth()->user()->load("followings");
        $user2 = $request->user2;

        $currentUserFollowUser = $authUser->followings->contains($user2);

        if ($currentUserFollowUser && !$authUser->isFollowingAndAccepted($user2)) {
            $authUser->unfollow($user2);
            return failed("your request is not Accepted Yet But Follow Request Is Deleted", [
                'deleted' => true,
                'was_aceepted' => false
            ]);
        }

        $authUser->toggleFollow($user2);
        return success([
            'deleted' => true,
            'was_accepeted' => true
        ]);
    }

    public function hasRequestedToFollow(HasRequestedToFollowRequest $request)
    {
        return success(['hasRequestedToFollow' => auth()->user()->hasRequestedToFollow($request->user_id)]);
    }

    public function acceptFollowRequestFrom(AcceptFollowRequestFromRequest $request)
    {
        $authUser = auth()->user()->load("followings");
        $user2 = $request->user2;

        if (!$user2->hasRequestedToFollow($authUser)) {
            return failed("{$user2->name} need to send you a follow request first");
        }

        $authUser->acceptFollowRequestFrom($user2);
        return success(['request_is_accepted' => true]);
    }

    public function rejectFollowRequestFrom(RejectFollowRequestFromRequest $request)
    {
        $authUser = auth()->user()->load("followings");
        $user2 = $request->user2;

        if (!$user2->hasRequestedToFollow($authUser)) {
            return failed("{$user2->name} need to send you a follow request first");
        }

        $authUser->rejectFollowRequestFrom($user2);
        return success(['request_is_rejected' => true]);
    }

    public function isFollowedByUser(IsFollowedByUserRequest $request)
    {
        $authUser = auth()->user()->load("followers");
        $user2 = $request->user2;
        $currentUserFollowedByUser = $authUser->followers->contains($user2);
        $userFollowRequestIsAccepted = $authUser->isFollowerAndAccepted($user2);
        if (!$currentUserFollowedByUser) {
            return success(['currentUserFollowedByUser' => false]);
        }
        return success(['currentUserFollowedByUser' => $currentUserFollowedByUser, 'userFollowRequestIsAccepted' => $userFollowRequestIsAccepted]);
    }

    public function isFollowUser(IsFollowUserRequest $request)
    {
        $authUser = auth()->user()->load("followings");
        $user2 = $request->user2;
        $currentUserFollowUser = $authUser->followings->contains($user2);
        $followRequestIsAccepted = $authUser->isFollowingAndAccepted($user2);
        if (!$currentUserFollowUser) {
            return success(['currentUserFollowUser' => false]);
        }
        return success(['currentUserFollowUser' => $currentUserFollowUser, 'followRequestIsAccepted' => $followRequestIsAccepted]);
    }

    public function areFollowingEachOther(AreFollowingEachOtherRequest $request)
    {
        return success(['areFollowingEachOther' => auth()->user()->areFollowingEachOther($request->user_id)]);
    }

    public function followingsList(FollowingsListRequest $request)
    {
        $followings = auth()->user()->followings()->paginate(10);
        return success(['followings' => $followings, 'total' => $followings->total()]);
    }

    public function followersList(FollowersListRequest $request)
    {
        $followers = auth()->user()->followers()->paginate(10);
        return success(['followers' => $followers, 'total' => $followers->total()]);
    }
}
