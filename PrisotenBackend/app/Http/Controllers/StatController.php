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

        $arrOfStudents = [];
        foreach ($archiveResults as $result) {
            array_push($arrOfStudents, $result->students);
        }


        //average attendance per subject group
        $results = DB::table('archive')
            ->join('subject_group', 'archive.subject_id', '=', 'subject_group.subject_id')
            ->join('Subject', 'subject_group.subject_id', '=', 'Subject.id')
            ->join('SchoolGroup', 'archive.school_group_id', '=', 'SchoolGroup.id')
            ->select(
                'Subject.name as subject_name',
                'subject_group.subject_id',
                'SchoolGroup.name as school_group_name',
                'subject_group.group_id',
                DB::raw('COUNT(DISTINCT archive.id) as total_lessons'),
                DB::raw('AVG(JSON_LENGTH(subject_group.logged_students)) as average_expected'),
                DB::raw('AVG(JSON_LENGTH(archive.students)) as average_actual'),
                DB::raw('AVG(JSON_LENGTH(archive.students) - JSON_LENGTH(subject_group.logged_students)) as average_extra'),
                DB::raw('AVG(JSON_LENGTH(subject_group.logged_students) - JSON_LENGTH(archive.students)) as average_missing')
            )
            ->groupBy('subject_group.subject_id', 'subject_group.group_id', 'Subject.name', 'SchoolGroup.name')
            ->get();

        //dd($results);



        $startDate = '2024-06-24';
        $endDate = '2024-08-07';

        // Query to count the number of students for each lesson within the date range, including subject name
        $lessons = DB::table('archive')
            ->join('subject_group', 'archive.subject_id', '=', 'subject_group.subject_id')
            ->join('Subject', 'subject_group.subject_id', '=', 'Subject.id')
            ->join('SchoolGroup', 'archive.school_group_id', '=', 'SchoolGroup.id')
            ->whereBetween('archive.created_at', [$startDate, $endDate])
            ->select(
                'archive.id',
                'archive.created_at',
                'Subject.name as subject_name',
                'SchoolGroup.name as school_group_name',
                DB::raw('JSON_LENGTH(archive.students) as student_count')
            )
            ->get();

        $lessonsArray = [];

        // Save results into the array
        foreach ($lessons as $lesson) {
            $lessonsArray[] = [
                'subject_name' => $lesson->subject_name,
                'lesson_id' => $lesson->id,
                'school_group_name' => $lesson->school_group_name,
                'date' => $lesson->created_at,
                'student_count' => $lesson->student_count,
            ];
        }

        // Output the array to verify the results
        //dd($lessonsArray);

        $groupedData = [];

        foreach ($lessonsArray as $entry) {
            $groupedData[$entry['subject_name']][] = $entry;
        }

        //dd($groupedData);

        return view('stat', [
            'average' => $results,
            'groupedData' => $groupedData
        ]);
    }
}

// DB subject_group je expected students 