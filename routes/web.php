<?php

use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

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
Route::get('/progress', [AttendanceController::class, 'getProgress'])->name('progress');
Route::get('/sendinitial', [MailerController::class, 'sendInitialMail'])->name('sendinitial');
Route::get('/notifapproval', [MailerController::class, 'getNotifApproval'])->name('notifapproval');
Route::post('/rejection', [MailerController::class, 'postRejectionReason'])->name('rejection');
Route::post('/uploadbase', [AttendanceController::class, 'uploadBase'])->name('uploadbase');
Route::post('/upload', [AttendanceController::class, 'upload'])->name('upload');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
