<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::post('/schedule-close-websocket', [RoomController::class, 'scheduleCloseWebSocket']);
Route::post('/update-close-websocket', [RoomController::class, 'updateScheduleCloseWebSocket']);

Route::post('/create-room', [RoomController::class, 'create']);
<<<<<<< HEAD
Route::post('/edit-room', [RoomController::class, 'edit']);
=======
Route::post('/join-room', [RoomController::class, 'join']);
>>>>>>> dd7d51533c572b6cbd569f0c979660e4eb9912d5

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/createRoom', [RoomController::class, 'classroomServe'])->middleware(['auth', 'verified'])->name('createRoom');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
