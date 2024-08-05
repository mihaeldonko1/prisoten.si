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
    public function statisticServe() {
        $userId = Auth::id();   //user id thats logged in   
        $results = DB::table('archive')->where('user_id', $userId)->get();
        $arr=[];
        foreach ($results as $result) {
            $sub_group = DB::table('subject_group')
            ->where('subject_id', $result->subject_id)
            ->where('group_id', $result->school_group_id)
            ->first();
            array_push($arr,$sub_group);
            $arr["asd"] = "123"; //bolš uporablat zaradi preglednosti
        }

        // dd($results[0]->id);
        // dd($results);
        dd($arr);
        return view('stat', ['statistics' => $results]);
    }
}