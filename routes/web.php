<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ClockController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\NotifyController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/getData', [ClockController::class ,  'index']);
Route::get('/api/data-from-database/{day?}', [DataController::class ,  'index']);
Route::get('/getFirst', [NotifyController::class ,  'getFirst']);
Route::get('/send', [NotifyController::class ,  'sendNotify']);
Route::get('/ha', [NotifyController::class ,  'isHoliday']);
Route::get('/notify_intro', function(){
    $pdfPath = public_path('pdf/Attendance_System_Line_Notify.pdf');
    return response()->file($pdfPath);
});

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile', [ProfileController::class, 'updateNotifyToken'])->name('profile.updateNotifyToken');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
