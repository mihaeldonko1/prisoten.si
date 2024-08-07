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

class DashboardController extends Controller
{
    public function dashboardInitialize(){
        $userId = Auth::id();
        
        $subject_groups = DB::table('subject_group')->where('user_id', $userId)->get();
    
        $result = [];
    
        foreach ($subject_groups as $subject_group) {
            $subject = DB::table('Subject')->where('id', $subject_group->subject_id)->first();
    
            $group = DB::table('SchoolGroup')->where('id', $subject_group->group_id)->first();
    
            $result[] = [
                'subject_group' => $subject_group,
                'subject' => $subject,
                'group' => $group,
            ];
        }
    
        return view("dashboard", [
            'results' => $result
        ]);
    }    
}
