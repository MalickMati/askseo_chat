<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SentMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $reciever;
    public $sender;

    public function __construct($message, $sender, $reciever)
    {
        $this->message = $message;
        $this->reciever = $reciever;
        $this->sender = $sender;
    }

    
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('private-channel.' . $this->reciever),
        ];
    }

    public function broadcastAs()
    {
        return 'message.received';
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
        ];
    }
}
