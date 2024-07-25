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
    function getStatistics() {
        $userId = Auth::id();
        $results = DB::table('archive')->where('user_id', $userId)->get();

        $dataArray = json_decode(json_encode($results), true);
        // To check the results
        //dd($dataArray);
        return view('statistics', ['statistics' => $dataArray]);
    }



    function getStudentStatistics(Request $request) {
        // Get the input and decode it if necessary
        $studentsInfo = $request->input('students');
    
        // Check if $studentsInfo is a JSON string and decode it
        if (is_string($studentsInfo)) {
            $studentsInfo = json_decode($studentsInfo, true);
        }
    
        Log::info($studentsInfo);
    
        // Initialize an empty array to store the students' data
        $studentsData = [];
    
        // Ensure $studentsInfo is an array before processing
        if (is_array($studentsInfo)) {
            // Loop through each student ID in the provided array
            foreach ($studentsInfo as $studentId) {
                // Query the database table "ucenci" to get the student data based on the ID
                $studentData = DB::table('ucenci')->where('id', $studentId)->first();
    
                // Store the queried data in the studentsData array
                if ($studentData) {
                    $studentsData[] = $studentData;
                }
            }
        }
    
        // Return the array containing all the students' data
        return response()->json($studentsData);
    }

    

}

