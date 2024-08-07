<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\StatController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\SubjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::post('/schedule-close-websocket', [RoomController::class, 'scheduleCloseWebSocket']);
Route::post('/update-close-websocket', [RoomController::class, 'updateScheduleCloseWebSocket']);

Route::post('/create-room', [RoomController::class, 'create']);
Route::post('/edit-room', [RoomController::class, 'edit']);
Route::post('/join-room', [RoomController::class, 'join']);

Route::get('/dashboard', [DashboardController::class, 'dashboardInitialize'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/createRoom', [RoomController::class, 'classroomServe'])->middleware(['auth', 'verified'])->name('createRoom');
Route::get('/stat', [StatController::class, 'statisticServe'])->middleware(['auth', 'verified'])->name('stat');
Route::get('/statistics', [StatisticsController::class, 'getStatistics'])->middleware(['auth', 'verified'])->name('statistics');
Route::post('/getStudentStatistics', [StatisticsController::class, 'getPopupModalStatistics'])->middleware(['auth', 'verified']);
Route::post('/removeStudentSession', [StatisticsController::class, 'removeStudentFromSession'])->middleware(['auth', 'verified']);
Route::post('/addStudentSession', [StatisticsController::class, 'addStudentFromSession'])->middleware(['auth', 'verified']);

Route::get('/subject/{id}', [SubjectController::class, 'show']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
