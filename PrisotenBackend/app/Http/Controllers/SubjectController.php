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

class SubjectController extends Controller
{
    public function show($id)
    {
        $userId = Auth::id();
    
        $subject_group = DB::table('subject_group')->where('user_id', $userId)->where('id', $id)->first();
    
        $subject_group_array = (array) $subject_group;
    
        $subject = DB::table('Subject')->where('id', $subject_group->subject_id)->first();
        $group = DB::table('SchoolGroup')->where('id', $subject_group->group_id)->first();
    
        $subject_array = (array) $subject;
        $group_array = (array) $group;
    
        $expectedStudents = json_decode($subject_group->logged_students, true);
    
        $archive = DB::table('archive')->where('user_id', $userId)->where('school_group_id', $subject_group->group_id)->where('subject_id', $subject_group->subject_id)->get();
    
        $totalHours = $archive->count();
    
        $studentAttendance = [];
        $expected_students = [];
        $extra_students = [];
    
        foreach ($expectedStudents as $studentId) {
            $expected_students[$studentId] = [
                'attendance_count' => 0,
                'attendance_percentage' => 0,
            ];
        }
    
        foreach ($archive as $record) {
            $students = json_decode($record->students, true);
    
            foreach ($students as $studentId) {
                if (isset($studentAttendance[$studentId])) {
                    $studentAttendance[$studentId]++;
                } else {
                    $studentAttendance[$studentId] = 1;
                }
            }
        }
    
        $allStudentIds = array_keys($studentAttendance);
    
        $studentData = DB::table('ucenci')->whereIn('id', array_merge($expectedStudents, $allStudentIds))->get()->keyBy('id');
    
        foreach ($expectedStudents as $studentId) {
            if (isset($studentAttendance[$studentId])) {
                $expected_students[$studentId]['attendance_count'] = $studentAttendance[$studentId];
                $expected_students[$studentId]['attendance_percentage'] = ($studentAttendance[$studentId] / $totalHours) * 100;
            }
    
            $studentInfo = (array) $studentData->get($studentId);
            $expected_students[$studentId] = array_merge($expected_students[$studentId], $studentInfo);
        }
    
        foreach ($studentAttendance as $studentId => $count) {
            if (!in_array($studentId, $expectedStudents)) {
                $studentInfo = (array) $studentData->get($studentId);
                $studentInfo['attendance_count'] = $count;
                $studentInfo['attendance_percentage'] = ($count / $totalHours) * 100;
                $extra_students[$studentId] = $studentInfo;
            }
        }


        return view('subject', [
            'subject_group' => $subject_group_array,
            'totalHours' => $totalHours,
            'subject' => $subject_array,
            'group' => $group_array,
            'expected_students' => $expected_students,
            'extra_students' => $extra_students,
        ]);
    }
    

        
}
