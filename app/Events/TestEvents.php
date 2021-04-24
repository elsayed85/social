<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TestEvents implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $user_id;
    public $payload;

    public function __construct($user_id, $payload)
    {
        $this->user_id = $user_id;
        $this->payload = $payload;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('App.Models.User.' . $this->user_id);
    }

    public function broadcastAs()
    {
        return "send_data_event";
    }

    public function broadcastWith()
    {
        return [
            'payload' => $this->payload,
            'created_at' => now()
        ];
    }
}
