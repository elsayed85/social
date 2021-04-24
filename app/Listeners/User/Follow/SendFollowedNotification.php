<?php

namespace App\Listeners\User\Follow;

use App\Events\TestEvents;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Overtrue\LaravelFollow\Events\Followed;

class SendFollowedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(Followed $event)
    {
        event(new TestEvents($event->followingId, ['followerId' => $event->followerId, 'followingId' => $event->followingId]));
    }
}
