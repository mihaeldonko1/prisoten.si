<?php

namespace App\Http\Controllers;

use App\Events\AttendanceRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Room;
use App\Jobs\CloseWebSocketJob;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use ReflectionClass;

class RoomController extends Controller
{
    public function create(Request $request)
    {
        $code = $request->input('code');
        $id = $request->input('id');
        $classroom = $request->input('classroom');
        $subject = $request->input('subject');
        $group = $request->input('group');
    
        $room = new Room;
        $room->user_id = $id;
        $room->code = $code;
        $room->active = true;
        $room->students = json_encode([]);  
        $room->classroom_id = $classroom;
        $room->subject_id = $subject;
        $room->school_group_id = $group;
        

        DB::table('rooms')->insert([
            'user_id' => $room->user_id,
            'code' => $room->code,
            'active' => $room->active,
            'students' => $room->students,
            'classroom_id' => $room->classroom_id,
            'created_at' => Carbon::now(), 
            'updated_at' => Carbon::now(), 
            'subject_id' => $room->subject_id,
            'school_group_id' => $room->school_group_id,
        ]);
    
        return response()->json(['roomCode' => $code, 'message' => 'Room created successfully']);
    }
    

    public function join(Request $request)
    {
        $email = $request->input('email');
        $name = $request->input('name');
        $code = $request->input('code');

        Log::info("joined the room");
    
        $existingUcenec = DB::table('ucenci')->where('email', $email)->first();
    
        if ($existingUcenec) {
            Log::info("Email already exists in ucenci table: " . $email);
            $this->addUserByCode($code, $existingUcenec->id);
            return response()->json(['message' => 'Email already exists in ucenci table', 'email' => $email]);
        } else {
            $newUserId = DB::table('ucenci')->insertGetId([
                'email' => $email,
                'name' => $name,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
    
            Log::info("Joined: " . $email);
            $this->addUserByCode($code, $newUserId);
            return response()->json(['message' => 'Joined room successfully', 'email' => $email]);
        }
    }
    
    public function addUserByCode($code, $userId)
    {
        $existingRoom = DB::table('rooms')->where('code', $code)->first();
        
        if ($existingRoom) {
            $roomId = $existingRoom->id;

            $currentStudents = json_decode($existingRoom->students, true);
    
            if (is_null($currentStudents)) {
                $currentStudents = [];
            }

            if (!in_array($userId, $currentStudents)) {
                $currentStudents[] = $userId;
            }

            DB::table('rooms')
                ->where('id', $roomId)
                ->update([
                    'students' => json_encode($currentStudents),
                    'updated_at' => Carbon::now()
                ]);
    
            return response()->json(['message' => 'User added to room successfully', 'room_id' => $roomId, 'ucenci' => $currentStudents]);
        } else {
            return response()->json(['error' => 'Room is not valid']);
        }
    }




    public function updateScheduleCloseWebSocket(Request $request)
    {
        $roomCode = $request->input('code');
        $timeLeft = $request->input('timeLeft');
        
        // Store the result of checkRoomCode in a variable
        $isRoomCodeValid = $this->checkRoomCode($roomCode);
        if ($isRoomCodeValid) {
            $this->withParamsScheduleCloseWebSocket($roomCode, $timeLeft);
        }
        // Log the result
        //Log::info($roomCode);
        //Log::info('Check Room Code Result:', ['isRoomCodeValid' => $isRoomCodeValid]);
    }


    public function checkRoomCode($roomCode)
    {
        // Query all jobs from the jobs table
        $jobs = DB::table('jobs')->get();

        foreach ($jobs as $job) {
            // Decode the payload JSON
            $payload = json_decode($job->payload, true);

            // Log the payload
            Log::info('Payload:', $payload);

            // Extract the command from the decoded payload
            if (isset($payload['data']['command'])) {
                $serializedCommand = $payload['data']['command'];

                // Log the serialized command string
                Log::info('Serialized Command:', [$serializedCommand]);

                // Unserialize the command
                $command = unserialize($serializedCommand);

                // Log the unserialized command object
                Log::info('Unserialized Command:', [$command]);

                // Use reflection to access the private/protected property 'roomCode'
                $reflection = new ReflectionClass($command);

                // Log the reflection object
                Log::info('Reflection:', [$reflection]);

                if ($reflection->hasProperty('roomCode')) {
                    $property = $reflection->getProperty('roomCode');

                    // Log the property object
                    Log::info('Property:', [$property]);

                    $property->setAccessible(true);
                    $commandRoomCode = $property->getValue($command);

                    // Log the value of roomCode
                    Log::info('Command RoomCode:', [$commandRoomCode]);

                    // Compare roomCode values
                    if ($commandRoomCode === $roomCode) {
                        // Delete the job from the jobs table
                        DB::table('jobs')->where('id', $job->id)->delete();

                        // Log the deletion
                        Log::info('Deleted job with id:', [$job->id]);

                        return true;
                    }
                } else {
                    // Log if the property is not found
                    Log::info('Property roomCode not found in command');
                }
            } else {
                // Log if command is not found in payload
                Log::info('Command not found in payload');
            }
        }

        // Log if no job matches the roomCode
        Log::info('No matching roomCode found in any job');

        return false;
    }

    public function getGroupsBySubject($subjectId)
    {
        $groups = DB::table('SchoolGroup')
                    ->where('subject_id', $subjectId)
                    ->get(['id', 'name']) // Adjust the fields as per your table structure
                    ->toArray();

        return response()->json($groups);
    }





    public function scheduleCloseWebSocket(Request $request)
    {
        $roomCode = $request->input('code');
        $timeLeft = $request->input('timeLeft');

        Log::info($timeLeft);
        CloseWebSocketJob::dispatch($roomCode)->delay(now()->addSeconds($timeLeft));

        return response()->json(['status' => 'Job scheduled', 'roomCode' => $roomCode, 'timeLeft' => $timeLeft]);
    }

    public function withParamsScheduleCloseWebSocket($givenCode, $givenTime)
    {
        $roomCode = $givenCode;
        $timeLeft = $givenTime;
        CloseWebSocketJob::dispatch($roomCode)->delay(now()->addSeconds($timeLeft));

        return response()->json(['status' => 'Job scheduled', 'roomCode' => $roomCode, 'timeLeft' => $timeLeft]);
    }
    
    public function classroomServe(){
        $classroomData = DB::table('ucilnica')->get();
        $userId = Auth::id();

        $classroomDataArray = json_decode(json_encode($classroomData), true);

        $SubjectGroupData = DB::table('subject_group')->where('user_id', $userId)->get();

        $SubjectGroupData = $SubjectGroupData->map(function ($item) {
            $subject = DB::table('Subject')->where('id', $item->subject_id)->first();
            $group = DB::table('SchoolGroup')->where('id', $item->group_id)->first();
        
            $item->subject = $subject;
            $item->group = $group;
        
            if ($subject && $group) {
                $item->fullName = $subject->name . ' (' . $group->name . ')';
            } else {
                $item->fullName = null; 
            }
        
            return (array) $item; // Convert each item to an array
        })->toArray();
        
       
        return view('createRoom', [
            'classrooms' => $classroomDataArray,
            'selectData' => $SubjectGroupData
        ]);
    }
    

}

