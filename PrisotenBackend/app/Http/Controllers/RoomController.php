<?php

namespace App\Http\Controllers;

use App\Events\AttendanceRoom;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function create(Request $request)
    {
        $code = $request->input('code');
        // No need to broadcast here since it's just room creation
        return response()->json(['roomCode' => $code]);
    }

    public function join(Request $request)
    {
        $code = $request->input('code');
        $name = $request->input('name');
        broadcast(new AttendanceRoom($code, $name))->toOthers();
        return response()->json(['message' => 'Joined room successfully', 'username' => $name]);
    }
}

