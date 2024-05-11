<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('attendanceRoom.{roomId}', function ($user, $roomId) {
    // Implement any necessary logic to determine if $user can listen on $roomId
    return true; 
});


