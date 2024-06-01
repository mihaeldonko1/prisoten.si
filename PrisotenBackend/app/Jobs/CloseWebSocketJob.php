<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CloseWebSocketJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $roomCode;

    /**
     * Create a new job instance.
     *
     * @param string $roomCode
     * @return void
     */
    public function __construct($roomCode)
    {
        $this->roomCode = $roomCode;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Fetch the room from the rooms table
        $room = DB::table('rooms')->where('code', $this->roomCode)->first();


        /// TODO UPATE JOB CHECK FOR TIMING


        if ($room) {
            if ($this->created_at==$this->updated_at){
                            // Insert the room into the archive table
            DB::table('archive')->insert([
                'user_id' => $room->user_id,
                'code' => $room->code,
                'active' => $room->active,
                'students' => $room->students,
                'classroom_id' => $room->classroom_id,
                'closed_at' => Carbon::now(), // Set the closed_at timestamp
                'created_at' => $room->created_at, // Preserve the original created_at timestamp
                'updated_at' => $room->updated_at, // Preserve the original updated_at timestamp
            ]);

            // Delete the room from the rooms table
            DB::table('rooms')->where('code', $this->roomCode)->delete();

            // Log the action
            echo "Closed websocket and moved room code: {$this->roomCode} to archive" . PHP_EOL;
        } else {
            DB::table('rooms')
            ->where('code', $roomCode)
            ->update(['updated_at' => $room->created_at]);
            echo "Room code: {$this->roomCode} (job scheduled at later time)" . PHP_EOL;
        }
    }
            else {
                echo "Room code: {$this->roomCode} not found" . PHP_EOL;
                
            }

    }
}
