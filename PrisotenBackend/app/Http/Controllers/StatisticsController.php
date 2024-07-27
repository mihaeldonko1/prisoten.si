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

class StatisticsController extends Controller
{
    public function getStatistics() {
        $userId = Auth::id();
        $results = DB::table('archive')->where('user_id', $userId)->get();

        $dataArray = json_decode(json_encode($results), true);

        return view('statistics', ['statistics' => $dataArray]);
    }

    public function getStudentPopupStatistics($studentsInfo) {
        if (is_string($studentsInfo)) {
            $studentsInfo = json_decode($studentsInfo, true);
        }
    
        $studentsData = [];
    
        if (is_array($studentsInfo)) {
            foreach ($studentsInfo as $studentId) {
                $studentData = DB::table('ucenci')->where('id', $studentId)->first();
                if ($studentData) {
                    $studentsData[] = $studentData;
                }
            }
        }

        return $studentsData;
    }

    public function getPopupModalStatistics(Request $request) {
        $studentsInfo = $request->input('students');
        $classroomInfo = $request->input('classroom');

        $classroomData = DB::table('ucilnica')->where('id', $classroomInfo)->first();

        $studentsData = $this->getStudentPopupStatistics($studentsInfo);

        return response()->json([
            'students' => $studentsData,
            'classroom' => $classroomData
        ]);
    }

    public function removeStudentFromSession(Request $request) {
        $room = $request->input('room');
        $student = $request->input('student');
    
        $studentsJson = null;
    
        $archive = DB::table('archive')->where('id', $room)->first();
        
        if ($archive) {
            $students = json_decode($archive->students, true);
    
            if (($key = array_search($student, $students)) !== false) {
                unset($students[$key]);
                $students = array_values($students);
                $studentsJson = json_encode($students);

                DB::table('archive')->where('id', $room)->update(['students' => $studentsJson]);
            }
        }
        if ($studentsJson) {
            $studentsData = $this->getStudentPopupStatistics($studentsJson);
            $archive = DB::table('archive')->where('id', $room)->first();
            return response()->json([
                'students' => $studentsData,
                'roomId' => $room,
                'result' => $archive 
            ]);
        } else {
            return response()->json(['message' => 'Student not found or no update needed'], 404);
        }
    }

    public function addStudentFromSession(Request $request) {
        $room = $request->input('room');
        $studentMail = $request->input('studentMail');
        
        // Check if the user with this email exists in the "ucenci" table
        $student = DB::table('ucenci')->where('email', $studentMail)->first();
        
        if ($student) {
            $studentId = $student->id;
        } else {
            // Extract the name from the email
            $emailParts = explode('@', $studentMail);
            $nameParts = explode('.', $emailParts[0]);
            
            // Remove any numeric characters from each part of the name
            $nameParts = array_map(function($part) {
                return preg_replace('/[0-9]+/', '', $part);
            }, $nameParts);
            
            // Ensure the extracted name remains consistent
            $name = ucwords(implode(' ', $nameParts));
    
            // Create a new user in the "ucilnica" table
            $studentId = DB::table('ucenci')->insertGetId([
                'email' => $studentMail,
                'name' => $name,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    
        // Retrieve the archive room
        $archive = DB::table('archive')->where('id', $room)->first();
    
        if ($archive) {
            // Decode the students array
            $students = json_decode($archive->students, true);
    
            // Check if the $studentId already exists in the students array
            if (!in_array($studentId, $students)) {
                // Add the $studentId to the students array
                $students[] = $studentId;
    
                // Encode the students array back to JSON
                $studentsJson = json_encode($students);
    
                // Update the archive table
                DB::table('archive')->where('id', $room)->update(['students' => $studentsJson]);
            }
        }

    

        if ($studentsJson) {
            $studentsData = $this->getStudentPopupStatistics($studentsJson);
            return response()->json([
                'students' => $studentsData,
                'roomId' => $room,
                'result' => $archive 
            ]);
        } else {
            return response()->json(['message' => 'Student not found or no update needed'], 404);
        }
    }
}