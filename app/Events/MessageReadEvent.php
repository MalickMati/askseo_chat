<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class MessageReadEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sender;

    public function __construct($sender)
    {
        $this->sender = $sender;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('private-channel.' . Auth::user()->id),
        ];
    }

    public function broadcastAs()
    {
        return 'message.read';
    }
}
