<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('attendanceRoom.{roomId}', function ($user, $roomId) {
    Log::info("tester1");
    return true; 
});


