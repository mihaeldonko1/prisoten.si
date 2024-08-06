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

class StatController extends Controller
{
    public function statisticServe()
    {
        $userId = Auth::id();   //user id thats logged in
        $archiveResults = DB::table('archive')->where('user_id', $userId)->get();

        $subjectGroupResults = DB::table('subject_group')
            ->where('user_id', $userId)
            ->get();

        $arrOfStudents = [];
        foreach ($archiveResults as $result) {
            array_push($arrOfStudents, $result->students);
        }


        //average attendance per subject group
        $results = DB::table('archive')
            ->join('subject_group', function ($join) {
                $join->on('archive.subject_id', '=', 'subject_group.subject_id');
            })
            ->select(
                'subject_group.subject_id',
                'subject_group.group_id',
                DB::raw('COUNT(DISTINCT archive.id) as total_lessons'),
                DB::raw('AVG(JSON_LENGTH(subject_group.logged_students)) as average_expected'),
                DB::raw('AVG(JSON_LENGTH(archive.students)) as average_actual'),
                DB::raw('AVG(JSON_LENGTH(archive.students) - JSON_LENGTH(subject_group.logged_students)) as average_extra'),
                DB::raw('AVG(JSON_LENGTH(subject_group.logged_students) - JSON_LENGTH(archive.students)) as average_missing')
            )
            ->groupBy('subject_group.subject_id', 'subject_group.group_id')
            ->get();

        // dd($results);


        $startDate = '2024-06-24';
        $endDate = '2024-07-24';

        // Query to count the number of students for each lesson within the date range
        $lessons = DB::table('archive')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('id', 'created_at', DB::raw('JSON_LENGTH(students) as student_count'))
            ->get();

            $lessonsArray = [];

            // Save results into the array
            foreach ($lessons as $lesson) {
                $lessonsArray[] = [
                    'lesson_id' => $lesson->id,
                    'date' => $lesson->created_at,
                    'student_count' => $lesson->student_count,
                ];
            }

        dd($lessonsArray);

        // $arr=[];
        // foreach ($archiveResults as $result) {
        //     $sub_group = DB::table('subject_group')
        //     ->where('subject_id', $result->subject_id)
        //     ->where('group_id', $result->school_group_id)
        //     ->first();
        //     array_push($arr,$sub_group);
        //     $arr["asd"] = "123"; //bolš uporablat zaradi preglednosti
        // }


        // dd($archiveResults[0]->id);
        // dd($archiveResults);
        //dd($arr);
        return view('stat', ['statistics' => $archiveResults]);
    }
}

// DB subject_group je expected students 