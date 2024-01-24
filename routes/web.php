<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailerController;
use App\Http\Controllers\AttendanceController;

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

Route::get('/', [AttendanceController::class, 'index']);
Route::get('/export', [AttendanceController::class, 'export'])->name('export');
Route::get('/progress', [AttendanceController::class, 'getProgress'])->name('progress');
Route::get('/series', [AttendanceController::class, 'getSeries'])->name('series');
Route::get('/ratiobyseries', [AttendanceController::class, 'getRatioBySeries'])->name('ratiobyseries');
Route::get('/sendinitial', [MailerController::class, 'sendInitialMail'])->name('sendinitial');
Route::get('/notifapproval', [MailerController::class, 'getNotifApproval'])->name('notifapproval');
Route::put('/rejection/{id}', [MailerController::class, 'postRejectionReason'])->name('rejection');
Route::post('/uploadbase', [AttendanceController::class, 'uploadBase'])->name('uploadbase');
Route::post('/upload', [AttendanceController::class, 'upload'])->name('upload');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
