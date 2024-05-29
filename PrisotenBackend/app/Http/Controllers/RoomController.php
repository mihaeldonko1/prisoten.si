<?php

namespace App\Http\Controllers;

use App\Events\AttendanceRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Room;
use App\Jobs\CloseWebSocketJob;

class RoomController extends Controller
{
    public function create(Request $request)
    {
        $code = $request->input('code');
        $name = $request->input('name');
    
        if (empty($name)) {
            $name = 'admin';
        }
    
        $room = new Room;
        $room->user_id = $name;
        $room->code = $code;
        $room->active = true;
        $room->students = json_encode([$name]);  // Initialize the users array with the creator
        $room->save();
    
        event(new AttendanceRoom($code, $name));
    
        return response()->json(['roomCode' => $code, 'message' => 'Room created successfully']);
    }
    

    public function join(Request $request)
    {
        $code = strtolower($request->input('code'));
        $name = $request->input('name');
        $roomName = 'attendanceRoom' . $code;
    
        $room = Room::where('code', $code)->where('active', true)->first();
    
        if (!$room) {
            //DISCONNECTAJ WEBSOCKET
            return response()->json(['error' => 'Room does not exist'], 404);  // Room must exist to join
        }
    
        // Decode the existing users array
        $users = json_decode($room->users, true);
    
        // Check if user already exists in the array
        if (in_array($name, $users)) {
            return response()->json(['error' => 'User already exists'], 409); // HTTP 409 Conflict
        }
    
        // Add the new user and save
        $students[] = $name;
        $room->students = json_encode($students);
        $room->save();
    
        event(new AttendanceRoom($code, $name));
    
        return response()->json(['message' => 'Joined room successfully', 'roomCode' => $code]);
    }
    
    public function scheduleCloseWebSocket(Request $request)
    {
        $roomCode = $request->input('code');
        $timeLeft = $request->input('timeLeft');

        // Dispatch the job to close the websocket after $timeLeft seconds
        CloseWebSocketJob::dispatch($roomCode)->delay(now()->addSeconds($timeLeft));
        //Log::info($roomCode);

        return response()->json(['status' => 'Job scheduled', 'roomCode' => $roomCode, 'timeLeft' => $timeLeft]);
    }
    
    
}

