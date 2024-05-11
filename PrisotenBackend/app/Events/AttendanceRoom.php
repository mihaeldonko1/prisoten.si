<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AttendanceRoom implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomId;
    public $username; 

    public function __construct($roomId, $username)
    {
        $this->roomId = $roomId;
        $this->username = $username;
    }

    public function broadcastOn()
    {
        Log::info("metoda poklicana". 'attendanceRoom.' . $this->roomId);
        return new Channel('attendanceRoom.' . $this->roomId);
    }
}

