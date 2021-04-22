<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\Block\BlockedUsersListRequest;
use App\Http\Requests\Api\User\Block\BlockRequest;
use App\Http\Requests\Api\User\Block\IsBlockedByUserRequest;
use App\Http\Requests\Api\User\Block\IsBlockingUserRequest;
use App\Http\Requests\Api\User\Block\IsMutualBlockMeRequest;
use App\Http\Requests\Api\User\Block\UnBlockRequest;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function block(BlockRequest $request)
    {
        if (auth()->user()->isBlocking($request->user_id)) {
            return failed("you already blocked him");
        }
        $block = auth()->user()->block($request->user_id);
        return response()->noContent();
    }

    public function unblock(UnBlockRequest $request)
    {
        if (!auth()->user()->isBlocking($request->user_id)) {
            return failed("you already Not blocked him");
        }
        $block = auth()->user()->unblock($request->user_id);
        return response()->noContent();
    }

    public function blockedUsersList(BlockedUsersListRequest $request)
    {
        $blockingUsers = auth()->user()->blockingUsers()->paginate(10);
        return success(['blockingUsers' => $blockingUsers, 'total' => $blockingUsers->total()]);
    }

    public function isBlockedByUser(IsBlockedByUserRequest $request)
    {
        return success(['blocked' => auth()->user()->isBlockedBy($request->user_id)]);
    }

    public function isBlockingUser(IsBlockingUserRequest $request)
    {
        return success(['blocked' => auth()->user()->isBlocking($request->user_id)]);
    }

    public function isMutualBlock(IsMutualBlockMeRequest $request)
    {
        return success(['is_mutual_block' => auth()->user()->isMutualBlock($request->user_id)]);
    }
}
