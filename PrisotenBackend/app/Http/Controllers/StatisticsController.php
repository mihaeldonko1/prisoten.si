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
    public function getDetailedResults($results) {
        $dataArray = json_decode(json_encode($results), true);
        
        foreach ($dataArray as &$item) {
            $subject = DB::table('Subject')->where('id', $item['subject_id'])->first();
            $item['subject'] = $subject ? $subject->name : null;
    
            $schoolGroup = DB::table('SchoolGroup')->where('id', $item['school_group_id'])->first();
            $item['school_group'] = $schoolGroup ? $schoolGroup->name : null;
    
            $subjectGroup = DB::table('subject_group')
                ->where('subject_id', $item['subject_id'])
                ->where('group_id', $item['school_group_id'])
                ->first();
    
            if ($subjectGroup) {
                $item['subject_group'] = json_decode(json_encode($subjectGroup), true);
            } else {
                $item['subject_group'] = null;
            }
    
            $students = json_decode($item['students'], true); 
            $loggedStudents = $item['subject_group'] ? json_decode($item['subject_group']['logged_students'], true) : [];
    
            $expectedStudentsJoined = array_intersect($loggedStudents, $students);
            $item['expected_students_joined'] = $expectedStudentsJoined;
            $item['expected_students_joined_count'] = count($expectedStudentsJoined);
    
            $expectedStudentsMissed = array_diff($loggedStudents, $students);
            $item['expected_students_missed'] = $expectedStudentsMissed;
            $item['expected_students_missed_count'] = count($expectedStudentsMissed);
    
            $extraStudents = array_diff($students, $loggedStudents);
            $item['extra_students'] = $extraStudents;
            $item['extra_students_count'] = count($extraStudents);
    
            $item['expected_students_count'] = count($loggedStudents);
            $item['joined_students_count'] = count($students);
        }

        return $dataArray;
    }

    public function getStatistics(Request $request)
    {
        $userId = Auth::id();
        
        $query = DB::table('archive')->where('user_id', $userId);

        $dateFrom = $request->has('date-from') ? Carbon::createFromFormat('d/m/Y', $request->input('date-from'))->format('Y-m-d') : null;
        $dateTo = $request->has('date-to') ? Carbon::createFromFormat('d/m/Y', $request->input('date-to'))->format('Y-m-d') : null;
    
        // Log the converted dates
        Log::info('Converted Date From:', [$dateFrom]);
        Log::info('Converted Date To:', [$dateTo]);
    
        $query = DB::table('archive')->where('user_id', $userId);
    
        if ($dateFrom && $dateTo) {
            Log::info('Using whereBetween with converted date-from and date-to');
            $query->whereBetween('updated_at', [$dateFrom, $dateTo]);
        } elseif ($dateFrom) {
            Log::info('Using where with converted date-from');
            $query->where('updated_at', '>=', $dateFrom);
        } elseif ($dateTo) {
            Log::info('Using where with converted date-to');
            $query->where('updated_at', '<=', $dateTo);
        }
    
        if ($request->has('subject')) {
            $query->where('subject_id', $request->input('subject'));
        }
    
        if ($request->has('group')) {
            $query->where('school_group_id', $request->input('group'));
        }
    
        $results = $query->get();
        $fullData = $this->getDetailedResults($results);
    
        $extra_data = DB::table('subject_group')->where('user_id', $userId)->get();
    
        $extra_data = $extra_data->transform(function ($item) {
            $subject = DB::table('Subject')->where('id', $item->subject_id)->first();
            $group = DB::table('SchoolGroup')->where('id', $item->group_id)->first();
            
            $itemArray = (array) $item;
            
            $itemArray['subject_details'] = $subject ? (array) $subject : null;
            $itemArray['group_details'] = $group ? (array) $group : null;
            
            if ($subject && $group) {
                $itemArray['full_name'] = $subject->name . ' - ' . $group->name;
            } else {
                $itemArray['full_name'] = null;
            }
            
            return $itemArray;
        })->toArray();
    
        if ($request->ajax()) {
            return view('partials._statistics', ['statistics' => $fullData])->render();
        }
    
        return view('statistics', [
            'statistics' => $fullData,
            'extra_data' => $extra_data
        ]);
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
        $studentsInfo = $request->input('students'); // Real students who joined
        $classroomInfo = $request->input('classroom'); // Assuming this is not used in this logic
        $expectedStudentsInfo = $request->input('expected_students'); // Expected students
    
        // Converting JSON strings to arrays (if needed)
        $studentsArray = json_decode($studentsInfo, true);
        $expectedStudentsArray = json_decode($expectedStudentsInfo, true);
    
        // Find joined students (students who were expected and actually came)
        $joined_students = array_intersect($expectedStudentsArray, $studentsArray);
    
        // Find extra students (students who came but were not expected)
        $extra_students = array_diff($studentsArray, $expectedStudentsArray);
    
        // Find missing students (students who were expected but did not come)
        $missing_students = array_diff($expectedStudentsArray, $studentsArray);
    
        // Ensure that all arrays are indexed arrays (remove keys)
        $joined_students = array_values($joined_students);
        $extra_students = array_values($extra_students);
        $missing_students = array_values($missing_students);

        $classroomData = DB::table('ucilnica')->where('id', $classroomInfo)->first();

        $joinedStudentsData = $this->getStudentPopupStatistics($joined_students);
        $extraStudentsData = $this->getStudentPopupStatistics($extra_students);
        $missingStudentsData = $this->getStudentPopupStatistics($missing_students);


        return response()->json([
            'joinedStudents' => $joinedStudentsData,
            'extraStudents' => $extraStudentsData,
            'missingStudents' => $missingStudentsData,
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
        Log::info($archive->school_group_id);

        $subject_group = DB::table('subject_group')
        ->where('subject_id', $archive->subject_id)
        ->where('group_id', $archive->school_group_id)
        ->first();

        Log::info(json_encode($subject_group));
        if($subject_group){
            $expected_students = $subject_group->logged_students;
            $expected_students_array = json_decode($expected_students, true);
        }else {
            $expected_students_array = [];
            Log::error('No expected students found');
        }

        $students_array = json_decode($studentsJson, true);

        if (is_array($expected_students_array) && is_array($students_array)) {
            $expected_students_in_class = array_intersect($expected_students_array, $students_array);
            $extra_students = array_diff($students_array, $expected_students_array);
            $missing_students = array_diff($expected_students_array, $students_array);

        } else {
            Log::error('Failed to decode JSON strings to arrays');
        }

        if ($expected_students_in_class || $extra_students || $missing_students) {
            $expected_studentsData = $this->getStudentPopupStatistics($expected_students_in_class);
            $extra_studentsData = $this->getStudentPopupStatistics($extra_students);
            $missing_studentsData = $this->getStudentPopupStatistics($missing_students);

            $combinedStudents = array_merge($expected_studentsData, $extra_studentsData, $missing_studentsData);

            Log::info(json_encode($combinedStudents));

            $combinedStudentIds = array_map(function($student) {
                return $student->id; 
            }, $combinedStudents);

            Log::info(json_encode($combinedStudents));

            $updatedArchive = DB::table('archive')->where('id', $room)->first();

            $formatedDataArchive = [];
            $formatedDataArchive[] = $updatedArchive;
            
            $results = $this->getDetailedResults($formatedDataArchive);
            
            return response()->json([
                'expected_students' => $expected_studentsData,
                'extra_students' => $extra_studentsData,
                'missing_students' => $missing_studentsData,
                'roomId' => $room,
                'result' => $results 
            ]);
        } else {
            return response()->json(['message' => 'Student not found or no update needed'], 404);
        }
    }

    public function addStudentFromSession(Request $request) {
        $room = $request->input('room');
        $studentMail = $request->input('studentMail');
        
        $student = DB::table('ucenci')->where('id', $studentMail)->first();
        
        if ($student) {
            $studentId = $student->id;
        } else {
            $emailParts = explode('@', $studentMail);
            $nameParts = explode('.', $emailParts[0]);
            
            $nameParts = array_map(function($part) {
                return preg_replace('/[0-9]+/', '', $part);
            }, $nameParts);
            
            $name = ucwords(implode(' ', $nameParts));

            $studentId = DB::table('ucenci')->insertGetId([
                'email' => $studentMail,
                'name' => $name,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    
        $archive = DB::table('archive')->where('id', $room)->first();
        $subject_group = DB::table('subject_group')
        ->where('subject_id', $archive->subject_id)
        ->where('group_id', $archive->school_group_id)
        ->first();

        if($subject_group){
            $expected_students = $subject_group->logged_students;
        }

        if ($archive) {
            $students = json_decode($archive->students, true);
    
            if (!in_array($studentId, $students)) {
                $students[] = $studentId;
    
                $studentsJson = json_encode($students);
    
                DB::table('archive')->where('id', $room)->update(['students' => $studentsJson]);
            }
        }




        $expected_students_array = json_decode($expected_students, true);
        $students_array = json_decode($studentsJson, true);

        if (is_array($expected_students_array) && is_array($students_array)) {
            $expected_students_in_class = array_intersect($expected_students_array, $students_array);
            $extra_students = array_diff($students_array, $expected_students_array);
            $missing_students = array_diff($expected_students_array, $students_array);

        } else {
            Log::error('Failed to decode JSON strings to arrays');
        }

        if ($expected_students_in_class || $extra_students || $missing_students) {
            $expected_studentsData = $this->getStudentPopupStatistics($expected_students_in_class);
            $extra_studentsData = $this->getStudentPopupStatistics($extra_students);
            $missing_studentsData = $this->getStudentPopupStatistics($missing_students);

            $combinedStudents = array_merge($expected_studentsData, $extra_studentsData, $missing_studentsData);

            $combinedStudentIds = array_map(function($student) {
                return $student->id; 
            }, $combinedStudents);

            $updatedArchive = DB::table('archive')->where('id', $room)->first();

            $formatedDataArchive = [];
            $formatedDataArchive[] = $updatedArchive;
            
            $results = $this->getDetailedResults($formatedDataArchive);

            Log::info(json_encode($results));
            
            return response()->json([
                'expected_students' => $expected_studentsData,
                'extra_students' => $extra_studentsData,
                'missing_students' => $missing_studentsData,
                'roomId' => $room,
                'result' => $results 
            ]);
        } else {
            return response()->json(['message' => 'Student not found or no update needed'], 404);
        }
    }
}