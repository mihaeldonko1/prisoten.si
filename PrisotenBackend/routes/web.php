<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;


Route::get('/', function () {
    return view('rooms.createRoom');
});

Route::post('/create-room', [RoomController::class, 'create']);
Route::post('/join-room', [RoomController::class, 'join']);